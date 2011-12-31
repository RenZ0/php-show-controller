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

"""Php-Show-Controller. SQL Class"""

import sys
import MySQLdb
from config import DB_HOST,DB_USER,DB_PASS,DB_BASE

class ComSql(object):
    def __init__(self):
        '''Initialisation, connexion sur la base'''

        #@ _CONNECT_
        try:
            self.conn = MySQLdb.connect (host = DB_HOST,
                                         user = DB_USER,
                                         passwd = DB_PASS,
                                         db = DB_BASE)
        except MySQLdb.Error, e:
            print "Error %d: %s" % (e.args[0], e.args[1])
            sys.exit (1)
        #@ _CONNECT_
        self.cursor = self.conn.cursor (MySQLdb.cursors.DictCursor)

    def requete_sql(self, req, *arg):
        '''Renvoi le resultat de la requete SQL donné en argument'''
        try:
        #@ _RETRIEVE_1_
            ret = self.cursor.execute(req, arg)
            return self.cursor.fetchall()

        #@ _CURSOR_CLOSE_
            self.cursor.close()
        #@ _CURSOR_CLOSE_

        except MySQLdb.Error, e:
            print "Error %d: %s" % (e.args[0], e.args[1])
            sys.exit (1)

#        #@ _TERMINATE_
#        conn.commit ()
#        conn.close ()
#        #@ _TERMINATE_

