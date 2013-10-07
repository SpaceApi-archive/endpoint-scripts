#! /usr/bin/env python

from wsgiref.util import setup_testing_defaults

# A relatively simple WSGI application. It's going to print out the
# environment dictionary after being updated by setup_testing_defaults
def spaceapi_app(environ, start_response):
    setup_testing_defaults(environ)

    status = '200 OK'
    headers = [('Content-type', 'text/plain; charset=utf-8')]

    start_response(status, headers)

    ret = [("%s: %s\n" % (key, value)).encode("utf-8")
           for key, value in environ.items()]
    return ret

######################################################################

## use this line to use the script with mod_wsgi or
## other wsgi-compliant web servers/modules
application = spaceapi_app

## use the following lines to run the script as built-in web server
#from wsgiref.simple_server import make_server
#httpd = make_server('', 8052, spaceapi_app)
#print("Serving on port 8052...")
#httpd.serve_forever()