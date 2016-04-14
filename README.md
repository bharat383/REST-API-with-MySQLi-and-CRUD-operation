# REST API with MySQLi - CRUD Operations
Rest API class with MySQLi based CRUD operations and User Module as demo.

# Developed By : 
Bharat Parmar

# Version : 
1.0

# File Structure :
1) config.php  : Configuration File 

2) bharatcode.sql : Database File.

3) class/Main.class.php : Main class file which contains many usefull methods for database operations, mail sending, validation.

4) rest/.htaccess : HTACCESS file for the URL redirection

5) rest/Rest.inc.php : This class file contains REST Standard basis api related methods.


# Requirements : 
1) PHP Version : 3.0 and above


Sample  Code :

1) Get Users :

Request : 

GET /bharat/restful/rest/users HTTP/1.1
Host: localhost
Cache-Control: no-cache
Postman-Token: 94ce58e8-5db7-4df4-19e5-457b29586d5f



2) Register User : 

Request : 

POST /bharat/restful/rest/register HTTP/1.1
Host: localhost
Cache-Control: no-cache
Postman-Token: ec8d2516-818d-4f3d-a417-9903575ccf81
Content-Type: application/x-www-form-urlencoded

Parameters : firstname, lastname, email, password
firstname=Jack&email=jackthomas@gmail.com&lastname=Thomas&password=123456

Response : 

{
  "status": "success",
  "message": "register successfully.",
  "data": {
    "user_id": 11
  }
}

3) Delete User :

Request : 

DELETE /bharat/restful/rest/deleteuser?id=11 HTTP/1.1
Host: localhost
Cache-Control: no-cache
Postman-Token: 79e1e8cb-60a8-993a-7e63-d2831ed9ac16
Content-Type: multipart/form-data;

Response : 

{
  "status": "success",
  "message": "Total 1 record(s) Deleted.",
  "data": 1
}

4) Login :

Request : 

POST /bharat/restful/rest/login HTTP/1.1
Host: localhost
Cache-Control: no-cache
Postman-Token: 651c7ef3-da80-0624-f519-b4ca8d39e8f5
Content-Type: application/x-www-form-urlencoded

Parameters : email, password
email=jackthomasgmail.com&password=123456

Response :

{
  "status": "success",
  "message": "logged in successfully.",
  "data": {
    "user_id": 11
  }
}
