# testing_php_mockme
Easy and simple website for mocking during testing. Only php is needed.

Within the web ui you add apicalls to the repository. 
In case of requesting the api.php these apicalls will be used to answer the requests.

## Introduction
The MockMe application runs on a simple webserver.

The requirements are:
- php 7.2 (recommended)
- http-user full access to the /data folder (read/write)

## Web UI
Call the following url to enter the webui of MockMe.

```
http://[url]/mockme/
```

There you can add new apicalls to the repository. These apicalls will be used to answer Requests.

## Api Call (Request)
Call the following url to send a request, which will be answered with the next available apicall from the repository.

```
http://[url]/mockme/api.php
```

## Note
This is a first draft of the MockMe application. There is no exception handling! Use it and debug it yourself.
