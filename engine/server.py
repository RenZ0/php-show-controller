#!/usr/bin/python
# -*- coding: utf-8 -*-

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

"""Php-Show-Controller. TCP Server to control scenarios."""

import SocketServer
from delta_testd import DmxSender
from config import HOST, PORT
#from threading import Lock
import time

# tcp server commands :
# start.n   : starts a thread playing sequence n one time
# stop.n    : stops a sequence
# status.n  : gives the status of a sequence
# list      : list of sequences currently playing
# stopall   : stops all sequences currently playing

class MyTCPServer(SocketServer.TCPServer):
    allow_reuse_address=True

class MyTCPHandler(SocketServer.BaseRequestHandler):
    """
    The RequestHandler class for our server.

    It is instantiated once per connection to the server, and must
    override the handle() method to implement communication to the
    client.
    """

#    global scen_dict
#    scen_dict = {}
#    lock=Lock()

    def handle(self):
        # self.request is the TCP socket connected to the client

        self.data = self.request.recv(1024).strip()
        status=0
        data=None
        try:
            command, scenarid = self.data.split('.')
        except:
            command=self.data

        self.ZeDelta = DmxSender()

        if command=="start":
            if not self.scen_dict.has_key(scenarid):
#                t= DeltaDmx(scenarid, lambda:self.terminate(scenarid))
                self.ZeDelta.scen_dict[scenarid]
#                t.start()
                status=1
        
        if command=="stop":
            try:
                t=self.scen_dict[scenarid]
                t.stop()
                t.join()
                if not t.is_alive():
                    status=1
            except:
                #import traceback
                #print traceback.format_exc()
                pass
            
        if command=="status":
            try:
                data=self.scen_dict[scenarid].state()
                status=1
            except:
                pass

        if command=="list":
            try:
                print self.scen_dict.keys()
                data=reduce(lambda x,y : y+'.'+x, self.scen_dict.keys())
            except:
                pass

        if command=="stopall":
            for t in self.scen_dict.itervalues():
                t.stop()
                status=1

        if command=="bo":
            for t in self.scen_dict.itervalues():
                t.stop()
                #status=1
            time.sleep(0.25)
            if not self.scen_dict.has_key(0):
                t= DeltaDmx(0, lambda:self.terminate(0))
                self.scen_dict[0]=t
                t.start()
                status=1

        response=str(status)
        if data is not None:
            response=response+":"+data

        self.request.send(response)
    
    def terminate(self,scenarid):
        if self.lock.acquire():
            self.scen_dict.pop(scenarid)
            self.lock.release()

if __name__=="__main__":

    # Create the server, binding to localhost on port 9999
    server = MyTCPServer((HOST, PORT), MyTCPHandler)

    # Activate the server; this will keep running until you
    # interrupt the program with Ctrl-C
    server.serve_forever()


