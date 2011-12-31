#! /usr/bin/python
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

"""Php-Show-Controller. Test for tcp server."""

import socket
import sys
import cgi
import time
from config import HOST, PORT

arguments=sys.argv
data=arguments[1]

try:
    # Create a socket (SOCK_STREAM means a TCP socket)
    sock = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
    
    # Connect to server and send data
    sock.connect((HOST, PORT))
    sock.send(data)
    
    # Receive data from the server and shut down
    received = sock.recv(1024)
    sock.close()

    print "Sent:     %s" % data
    print "Received: %s" % received
except:
    print "Server not ready"
