---
layout: post
headline: Usergrid - Build Awesome Apps using API-Driven Backend Service
title: Usergrid - Build Awesome Apps using API-Driven Backend Service
keywords: Rod Simpson, BaaS, Apigee, APIs
description: Rod Simpson - Usergrid - Build Awesome Apps using API-Driven Backend Service
date: 2012-09-11 17:00:00 -06:00
image: /images/blog/train.jpg
credit: Train, <a href="http://rodsimpson.com">Rod Simpson</a> copyright 2012
cats:
  - Tech Stuff
  - Software
tags:
  - BaaS
  - Software
  - APIs
---

##Getting Started
In my previous article, I discussed the emergence of Backend as a Service (BaaS) as a solution for the traditional server side stack. Instead of spending months building a backend, mobile and web app developers can now leverage services to help them deliver higher quality apps in less time than ever before. In this post we look at a few of the common features that a BaaS solution brings to the table. We will also show the API endpoints that developers can use to take advantage of these features in their apps.

##What do mobile apps need?
In today's competitive marketplace, users are no longer content with apps that simply "do" something; they want engaging apps that enable them to connect with other users and access interesting data.

As an example, suppose we want to create a new photo-sharing app. The nature of this type of app implies that we will need a backend, as we will obviously need to store the photos somewhere.  But beyond simple storage, our photo-sharing app could be greatly enhanced by many of the features and services available from a BaaS.

##Building a photo-sharing app the API way
Because a BaaS solution like Usergrid is simply another API that your app interacts with, it is easy to integrate into your app. Calls to the service are made via standard RESTful HTTP calls (GET, POST, PUT, and DELETE), and always return easy-to-use JSON objects.

The following examples illustrate the endpoints in Usergrid.

Note: our example URIs are truncated for brevity—for example:

	http://api.usergrid.com/<org_name>/<app_name>/users is shortened to /users.

##User Registration
One of the first things our new app needs is user registration. You can easily create and access users via standard REST calls:

	POST /users {"username":"rodsimpson", "name":"Rod Simpson"…}
	GET /users/rodsimpson

##Data Storage
Our app also needs custom data storage for things like photos, meta-data, and more. Create any type of collection easily.

	POST / {"data":"my data"}
	POST /cameras {"name":"Canon 5D", "type":"digital"…}

##Posting Comments & Other Activities
Users may also like to post comments on photos. You can build a feature like this into your app easily using Activities.

	POST /activities/ {"comment":"Great lighting!",…}

##Social Graph
Another important feature that users expect is social networking. Users can follow other users to create a social graph, or any type of custom relationship can be established.

	POST /users/rod/follows/users/mary
	POST /users/rod/likes/photos/marysphoto

##Geolocation
Geolocation is another popular feature of modern apps and can be of great use in our photo-sharing app. For example, users may like to search for photos that were taken nearby, or even further narrow their search to include only photos that were taken by a specific person.

	GET /photos?ql=location within 16093 of 37.776753, -122.407846
	GET /photos?ql=taken_by contains 'ed' and within 5000 of 37.803, -122.404

Users today also expect that they can log in with their existing social media accounts, like Facebook or Twitter.

	/auth/facebook/fb_access_token= /users?filter=facebook.first_name eq 'john'

Users may also like to see comments and photos that other users have taken.

	GET /users/mary/feed

##Point, shoot, build app
Many developers today are already channeling the magic of APIs to power their apps. Usergrid gives developers the back-end features they need as services and eliminates the overhead and expense of writing server-side code and building the backend. It’s simply another way to ease the development process and make your apps appealing and engaging so that people want to keep using them. What a solution like Usergrid does best is get app developers building awesome apps quickly.


**Originally posted on [Apigee's Best Practices Blog](https://blog.apigee.com/usergrid_build_awesome_apps_using_api_driven_backend_service)**