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
from threading import Thread
from ola.ClientWrapper import ClientWrapper

###

class DmxSender:
    def __init__(self, wrapper, universe):
        self._wrapper = wrapper
        self._universe = universe
        self.base = com_sql.ComSql()

        engine = self.base.requete_sql("SELECT * FROM dmx_engine WHERE id=1") #setting
        for e in range(len(engine)):
            freq_ms = engine[e]['freq_ms']

        self._tick_interval = int(freq_ms)  # in milliseconds

        print "freq_ms"
        print self._tick_interval

        # send the first one
        self.SendDmx()
        self._wrapper.Run()

    def SendDmx(self):

        #Schedule an event to run in the future
        self._wrapper.AddEvent(self._tick_interval, self.SendDmx)

        #for each scenari in DICT
            PlayScenari(id_scenari).Run
            PlayScenari(id_scenari).ComputeNextFrame

        MakeTheWholeFrame = map(add_each_frame) # TO DO

        data = array.array('B', new_frame)
        self._wrapper.Client().SendDmx(self._universe, data, self.DmxComplete)

        if self._activesender:
            # continue
            #for each scenari in DICT
            PlayScenari(id_scenari).ComputeNextFrame

        else:
            self._wrapper.Stop()

###

class BlackOut:
    def __init__(self):
        print "blackout"

        trame=""
        for i in range(512):
            trame+="0."
        trame=trame[:-1]
        print trame

        ei=[int(k) for k in trame.split(".")]

#        ### send dmx ###

#        #boucle hold
#        h = 3
#        h_ms = int(float(h)*1000)
#        print h_ms
#        self.sender.Run(ei, ei, self._tick_interval, h_ms)

class PlayScenari:
    def __init__(self, scenari):
        self._scenari = scenari
        self._activesender = True
        self.base = com_sql.ComSql()
        self.GetFixtureDetails

    def GetFixtureDetails(self):
        scendet = self.base.requete_sql("SELECT * FROM dmx_scensum WHERE id=%s", str(self.scenari)) #scen
        for i in range(len(scendet)):
            self.reverse = scendet[i]['reverse']

            if self.reverse==0:
                way="ASC"
            else:
                way="DESC"

            print way

            fixtdet = self.base.requete_sql("SELECT * FROM dmx_fixture WHERE id=%s", str(scendet[i]['id_fixture'])) #fixt
            for j in range(len(fixtdet)):
                self.patch = fixtdet[j]['patch']
                self.patch_after = fixtdet[j]['patch_after']
                self.universe = fixtdet[j]['univ']
        print "patch, patch_after, univ"
        print self.patch, self.patch_after, self.universe

        self.poffset=""
        for i in range(patch):
            self.poffset+="0."
        print self.poffset

        self.pafter=""
        for i in range(patch_after):
            self.pafter+="0."
        print self.pafter

    def GetScenariSeq(self):
        #sequence
        if reverse==0:
            sequence = self.base.requete_sql("SELECT * FROM dmx_scenseq WHERE disabled!=1 AND id_scenari=%s ORDER BY position ASC,id ASC", str(self.scenari)) #seq
        else:
            sequence = self.base.requete_sql("SELECT * FROM dmx_scenseq WHERE disabled!=1 AND id_scenari=%s ORDER BY position DESC,id DESC", str(self.scenari)) #seq

        #t = sequence[0]['fade']
        #h = sequence[0]['hold']

        # define first step
        si = ""

        for i in range(len(sequence)):
            #print (sequence[i]['step'])
            #print "len"
            #print (len(sequence))

            print "start"
            print i
            # init
            if si == "":
                start_step = sequence[i]['id']
                si = self.GetDmxTrame(start_step)

            #print (len(sequence))
            if (i+1)==(len(sequence)):
                i=-1
                print "reloop"

            print "end"
            print i+1
            end_step = sequence[i+1]['id']
            ei = self.GetDmxTrame(end_step)

                ### FADE ###

                t = sequence[i]['fade']
                self._checkpoint()
                print "fade"
                print (t)

                ### send dmx ###

                if float(t) > 0:
                    #boucle fade
                    t_ms = int(float(t)*1000)
                    print t_ms
                    self.sender.Run(si, ei, self._tick_interval, t_ms)
                else:
                    pass

                ### HOLD ###

                h = sequence[i+1]['hold']
                self._checkpoint()
                print "hold"
                print (h)

                ### send dmx ###

                if float(h) > 0:
                    #boucle hold
                    h_ms = int(float(h)*1000)
                    print h_ms
                    self.sender.Run(ei, ei, self._tick_interval, h_ms)
                else:
                    pass

                # next step start from end
                si = ei

    def GetDmxTrame(self, step_id):
        alldmx=self.poffset
        tramedmx = self.base.requete_sql("SELECT * FROM dmx_scenari WHERE id_scenari=%s AND step=%s ORDER BY id", str(self.scenari), str(step_id)) #step2
        for k in range(len(tramedmx)):
            #print(tramedmx[k]['ch_value'])
            alldmx+=tramedmx[k]['ch_value']
            alldmx+="."
        alldmx+=self.pafter
        alldmx=alldmx[:-1]
        print alldmx
        return [int(k) for k in alldmx.split(".")]

    def Run(self, start, end, tick_interval, fade_interval):
        self._counter = 0
        self._tick_interval = tick_interval
        self._frame = start
        self._ticks = float(fade_interval) / tick_interval                                                                                      
        #print self._ticks
        self._delta = [float(b - a) / self._ticks for a, b in zip(start, end)]

        if self._counter == self._ticks:
            self.gen_dmx.next_step # TO DO

    def ComputeNextFrame(self):
        self._counter += 1
        self._frame = map(sum, zip(self._frame, self._delta))
        self.new_frame = [int(round(x)) for x in self._frame]
        #print "sending %s" % new_frame

    def DmxComplete(self, state):
        if not state.Succeeded() or self._counter > self._ticks:
            #sleep 25ms to avoid rising between steps
            norise = (float(self._tick_interval)/1000)
            #print "sleep"
            #print norise
            time.sleep(norise)
            self._wrapper.Stop()
            #print "stop sender"

    def StopDmxSender(self):
        self._activesender = False

###



###

class ExcStopDmx(Exception):
    pass

class ThreadDmx(Thread):
    def __init__(self, term):
        Thread.__init__(self)
        self.Terminated = False
        self.term=term

    def run(self):
        pass

    ## Stop the thread if called
    def stop(self):
        self.Terminated = True

    def _checkpoint(self):
        if self.Terminated:
            raise ExcStopDmx

class DeltaDmx(ThreadDmx):

    def __init__(self, scenari, term):
        self.scenari = scenari
        super(DeltaDmx,self).__init__(term)

    def run(self):
        print self.getName(),"Started"    
        try:
            if self.scenari != 0:
                self.gen_dmx()
            else:
                self.blackout()
        except ExcStopDmx:
            print self.getName(),"Stopped"
            return
        finally:
            self.term()
        print self.getName(),"Finished"

    def state(self):
        return self.getName()+" running "

    def stop(self):
        super(DeltaDmx,self).stop()
        if hasattr(self, 'sender') and self.sender:
            #print "sender will stop now"
            self.sender.StopDmxSender()





    def gen_dmx(self):

        ##################

        ##################

        wrapper = ClientWrapper()
        self.sender = DmxSender(wrapper, universe)

        ##################



