# Question state caching

The current cache is very close indeed to the CAS.  Indeed, it is actually calls to the CAS itself which are cached.  This gives a dramatic increase in performance when the result already exists. 

We recommend using the cache on a production server.  Note, the cache needs to be cleared manually, perhaps on an annual update cycle.

## Clearing the cache ##

This can be done through Moodle.

Navigate to

     Home > Site administration > Plugins > Question types > STACK

Choose to view the healthcheck page.  At the bottom of this is a button to clear the cache.



