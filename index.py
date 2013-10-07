#! /usr/bin/env python

#from wsgiref.util import setup_testing_defaults

# A relatively simple WSGI application. It's going to print out the
# environment dictionary after being updated by setup_testing_defaults
def spaceapi_app(environ, start_response):

    #setup_testing_defaults(environ)

    status = '200 OK'
    headers = [('Content-type', 'text/plain; charset=utf-8')]

    start_response(status, headers)

    request_uri = environ['REQUEST_URI']

    controller = Router.get_controller()
    return controller

    #print(test)
    #print('test')
    #print >> environ['wsgi.errors'], "application debug #2"

    ret = request_uri
    #ret = [("%s: %s\n" % (key, value)).encode("utf-8")
    #       for key, value in environ.items()]
    return ret


class Router():

    @staticmethod
    def get_router_segment(index):
        return ''

    @staticmethod
    def get_controller():
        return 'asdfasdfasdf'

    @staticmethod
    def get_action():
        return 'asdfasdf'


class Output():

    @staticmethod
    def html():
        return ''

    @staticmethod
    def json():
        return ''


class Sensor():

    @staticmethod
    def save(sensors):
        return ''

######################################################################

## use this line to use the script with mod_wsgi or
## other wsgi-compliant web servers/modules
application = spaceapi_app

## use the following lines to run the script as built-in web server
#from wsgiref.simple_server import make_server
#httpd = make_server('', 8052, spaceapi_app)
#print("Serving on port 8052...")
#httpd.serve_forever()

######################################################################

# http://docs.python.org/3.3/library/wsgiref.html
# https://wiki.archlinux.org/index.php/Mod_wsgi
# https://code.google.com/p/modwsgi/wiki/DebuggingTechniques