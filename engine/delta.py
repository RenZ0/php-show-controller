#!/usr/bin/python
# -*- coding:utf-8 -*-

#  This program is free software; you can redistribute it and/or modify
#  it under the terms of the GNU General Public License as published by
#  the Free Software Foundation; either version 2 of the License, or
#  (at your option) any later version.
#
#  This program is distributed in the hope that it will be useful,
#  but WITHOUT ANY WARRANTY; without even the implied warranty of
#  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#  GNU Library General Public License for more details.
#
#  You should have received a copy of the GNU General Public License
#  along with this program; if not, write to the Free Software
#  Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
#
# Copyright (C) 2011 Laurent Pierru <renzo@imaginux.com>
# Thanks to Stéphane Bonhomme <stephane@exselt.com>
# Thanks to Simon Newton <nomis52@gmail.com>

"""Php-Show-Controller. Generates values and sends data to OLA."""

import sys
import time
import array
import com_sql
from ola.ClientWrapper import ClientWrapper

###

class DmxSender:
    def __init__(self, wrapper):
        '''Wrapper, Framerate'''

        self._wrapper = wrapper
        self._activesender = True
        self.base = com_sql.ComSql()

        # SQL Framerate
        engine = self.base.requete_sql("SELECT * FROM dmx_engine WHERE id=1") #setting
        for e in range(len(engine)):
            freq_ms = engine[e]['freq_ms']

        # FOR TEST
#        freq_ms = 500

        self._tick_interval = int(freq_ms)  # in milliseconds

        print "freq_ms"
        print self._tick_interval

        # FOR TEST
        self.scen_list=(1,2)

        # dict to store each scenari instance
        self.my_scens={}

        # SQL Universes
        prefs = self.base.requete_sql("SELECT * FROM dmx_preferences WHERE id=1") #setting
        for p in range(len(prefs)):
            self.univ_qty = prefs[p]['univ_qty']

        # array to store full frame
        self.WholeDmxFrame = [0] * 512 * self.univ_qty

        # send the first one
        self.SendDmxFrame()
        self._wrapper.Run()

    def AssignChannels(self,offset,values):
        '''Assign channels values according to address'''
        self.WholeDmxFrame[offset:offset+len(values)] = values

    def SendDmxFrame(self):
        '''Ask frame for each scenari and make the whole frame, repeated every tick_interval'''

        if self._activesender:
            # Schedule an event to run in the future
            print "Schedule next"
            self._wrapper.AddEvent(self._tick_interval, self.SendDmxFrame)

        else:
            self._wrapper.Stop()

        #for each scenari in list
        for scenarid in self.scen_list:

            # create scenari instance if needed
            if not self.my_scens.has_key(scenarid):
                scen=PlayScenari(scenarid, self._tick_interval)

                # store instance in dict, only once
                self.my_scens[scenarid]=scen
                print self.my_scens

            # for each instance, compute frame
            scen=self.my_scens[scenarid]
            scen.ComputeNextFrame()
            print "ComputeNextFrame"
#            print "sending %s" % scen.new_frame

            # add partial frame to full one
            self.AssignChannels(scen.patch, scen.new_frame)

#            print "FRAME"
#            print self.WholeDmxFrame

        # send data to universes
#        print "SPLIT"
        SplittedFrame = self.split(self.WholeDmxFrame,512)

        u=1
        for FramePart in SplittedFrame:
            UniverseFrame = list(FramePart)
            print "FRAME_FOR_UNIV %s" % u
            print UniverseFrame
            data = array.array('B', UniverseFrame)
            self._wrapper.Client().SendDmx(u, data)
            u = u+1

    def StopDmxSender(self):
        self._activesender = False

    def split(self, l, n):
        return zip(*(l[i::n] for i in range(n)))

###

#class BlackOut:
#    def __init__(self):
#        '''Send zeros on all channels'''

#        frame=""
#        for i in range(512):
#            frame+="0."
#        frame=frame[:-1]
#        print frame

#        ei=[int(k) for k in frame.split(".")]

###

class PlayScenari:
    def __init__(self, scenari, tickint):
        '''Each instance if for only one scenari'''

        self.scenari = scenari
        self.tick_interval = tickint
        self._activescenari = True
        self.base = com_sql.ComSql()
        self.GetFixtureDetails()
        self.current_i = -1
        self.GetNextStep()

    def GetFixtureDetails(self):
        '''Fixture patch (define address), universe'''

        # SQL Scen infos
        scendet = self.base.requete_sql("SELECT * FROM dmx_scensum WHERE id=%s", str(self.scenari)) #scen
        for i in range(len(scendet)):

            # SQL Fixture infos
            fixtdet = self.base.requete_sql("SELECT * FROM dmx_fixture WHERE id=%s", str(scendet[i]['id_fixture'])) #fixt
            for j in range(len(fixtdet)):
                self.patch = fixtdet[j]['patch']
                self.patch_after = fixtdet[j]['patch_after']
                self.universe = fixtdet[j]['univ']
        print "patch, patch_after, univ"
        print self.patch, self.patch_after, self.universe

        # change patch to meet universe zone
        if self.universe > 1:
            self.patch = self.patch + (512 * (self.universe-1))

        # fill zeros if splitted fixture
        self.pafter=""
        for i in range(self.patch_after):
            self.pafter+="0."
        print self.pafter

    def GetNextStep(self):
        '''Define the next step, fade/hold times, target values and delta'''

        print "Define the next step for scenari %s" % self.scenari

        # SQL Scen infos
        scendet = self.base.requete_sql("SELECT * FROM dmx_scensum WHERE id=%s", str(self.scenari)) #scen
        for i in range(len(scendet)):
            self.reverse = scendet[i]['reverse']

            if self.reverse==0:
                way="ASC"
            else:
                way="DESC"

            print way

        # SQL Sequence
        if self.reverse==0:
            sequence = self.base.requete_sql("SELECT * FROM dmx_scenseq WHERE disabled!=1 AND id_scenari=%s ORDER BY position ASC,id ASC", str(self.scenari)) #seq
        else:
            sequence = self.base.requete_sql("SELECT * FROM dmx_scenseq WHERE disabled!=1 AND id_scenari=%s ORDER BY position DESC,id DESC", str(self.scenari)) #seq

        # each time we call this function, increase i to get the next step of sequence
        self.current_i = self.current_i +1
        print "current i in seq"
        print self.current_i

        # milliseconds
        self.hold_interval=int(float(sequence[self.current_i]['hold'])*1000)
        self.fade_interval=int(float(sequence[self.current_i]['fade'])*1000)

        print "start"
        self.start_stepid=sequence[self.current_i]['id']
        print self.start_stepid

#        print "len seq"
#        print (len(sequence))

        if (self.current_i +1) == (len(sequence)):
            self.current_i = -1
            print "reloop"

        print "end"
        self.end_stepid=sequence[self.current_i +1]['id']
        print self.end_stepid

        # compose frame for step and next step
        self.start=self.GetDmxFrame(self.start_stepid)
        self.end=self.GetDmxFrame(self.end_stepid)

        # define first frame
        self._frame = self.start

        # hold : reset counter and define ticks
        self.hold_ticks = float(self.hold_interval) / self.tick_interval
        self.hold_counter = self.hold_ticks

        # fade : reset counter and define ticks
        self.fade_ticks = float(self.fade_interval) / self.tick_interval
        self.fade_counter = self.fade_ticks

        # if not zero fade, define delta
        if self.fade_ticks != 0:

            self._delta = [float(b - a) / self.fade_ticks for a, b in zip(self.start, self.end)]
            print "delta"
            print self._delta

            print "Iter"
            print self.fade_ticks

#        # next step start from end
#        self.start = self.end

    def GetDmxFrame(self, step_id):
        '''Compose frame for one step'''

        alldmx=""
        framedmx = self.base.requete_sql("SELECT * FROM dmx_scenari WHERE id_scenari=%s AND step=%s ORDER BY id", str(self.scenari), str(step_id)) #step2
        for k in range(len(framedmx)):
            #print(framedmx[k]['ch_value'])
            alldmx+=framedmx[k]['ch_value']
            alldmx+="."
        alldmx+=self.pafter
        alldmx=alldmx[:-1]
        print alldmx
        return [int(k) for k in alldmx.split(".")]

    def ComputeNextFrame(self):
        '''Return frame according to hold and fade'''

        if self._activescenari:
            # play hold first
            if self.hold_counter != 0:

                self.hold_counter -= 1
                print "hold counter"
                print self.hold_counter

                # start frame
                self.new_frame = [int(round(x)) for x in self._frame]

            else:
                pass

            # play fade after
            if self.hold_counter == 0 and self.fade_counter != 0:

                self.fade_counter -= 1
                print "fade counter"
                print self.fade_counter

                # compute fade frame
                self._frame = map(sum, zip(self._frame, self._delta))
                self.new_frame = [int(round(x)) for x in self._frame]

            else:
                pass

            # if all completed, call the next step
            if self.hold_counter == 0 and self.fade_counter == 0:
                print "NEXT STEP"
                self.GetNextStep()

        else:
            print "Stop"

    def StopScenari(self):
        self._activescenari = False

        ##################

wrapper = ClientWrapper()
sender = DmxSender(wrapper)

        ##################

