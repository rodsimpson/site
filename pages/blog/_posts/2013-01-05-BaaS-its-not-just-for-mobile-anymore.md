---
layout: post
headline: Backend as a Service - Not Just for Mobile Anymore
title: Backend as a Service - Not Just for Mobile Anymore
keywords: Rod Simpson, BaaS, Apigee, APIs
description: Rod Simpson - Backend as a Service - Not Just for Mobile Anymore
date: 2014-01-05 17:00:00 -06:00
image: /images/blog/truck.jpg
credit: Snowy Truck, <a href="http://rodsimpson.com">Rod Simpson</a> copyright 2013
cats:
  - Tech Stuff
  - Software
tags:
  - BaaS
  - Software
  - APIs
---

The Backend as a Service (BaaS) revolution has mainly targeted mobile developers (yes, there’s an acronym for that: mBaaS), and for good reason. A mobile developer’s skills typically don’t overlap those required for building and maintaining server-side infrastructure. The same isn’t true for web app developers. They live and breathe on the server. So why is a BaaS a good solution for web apps? The answer is simple: time and money.

##Features are costly
Surprisingly, advanced web applications require much of the same functionality that mobile apps do. User accounts, data storage, role-based access, and social features such as groups, connections, and activity feeds are all common needs for today’s web applications.

Competent server-side developers could build any of these features into their web app. Any single feature might not be an excessive engineering task, but taken together, the system soon becomes large and complex. Building these features takes time and costs money. So why build them if you don’t have to?

##Enter BaaS
BaaS is typically pitched as a complete replacement for a server-side stack. But if you’re building a web app using something like PHP, Ruby, or .Net, you aren’t getting rid of your server. In this case, a BaaS can be viewed as a supplement, not a replacement. Think of it as another database, albeit a very full-featured one.

By using a BaaS instead of, say, MySQL, half of the total work of the system is already done for a PHP developer, right at the outset. Application logic and page generation will still need to be completed, just like a mobile developer must do when using a BaaS. But instead of needing to build the standard necessities, such as user management or permissions schemes, these are ready out-of-the-box.

As an example, user management becomes as simple as building a user settings page, a login page, and a signup page. No database schema, no classes to manage passwords and encryption, no code to count login attempts, no session management to keep track of login status. The BaaS handles all of this. The developer really only needs to worry about the UI.

#Adding mobile capability to your new web app
Another compelling reason for using a BaaS to power your web application is that adding a dedicated mobile app is simple. Companies often want to pursue a two-pronged approach that includes both a web app and a dedicated mobile offering. For server-side developers, this would typically mean lots of extra work in the form of exposing all the server-side functionality as a REST tier.

Why a REST tier? Because mobile devices can’t talk directly to the MySQL database—this would require embedding the database credentials directly in the mobile app, which is a bad idea (reverse engineering and all that, and what if the credentials need to change?). Today’s standard is to create a RESTful service for the mobile app to talk to, something the server-side developers would have to build.

The server-side developers will already have classes for interacting with the database, but now they need to expose these operations as REST endpoints that send and receive JSON. They will need to build, for example, an endpoint for signing up new users, an endpoint for logging in these new users, an endpoint for editing the accounts of these new users, and so on. If these same server-side developers had leveraged a BaaS from the start, they would not have to build out any of this new functionality. The BaaS already is a RESTful service, ready to talk to mobile apps.

These server-side developers would only need to build the mobile app.

#Great! How do I get started?
So how does a server-side developer take advantage of a BaaS?  The most important thing is to make sure that your BaaS offers an SDK in the language you want to work with. Having a framework to help connect your web app to the BaaS will save a lot of time and will ease integration. Apigee offers SDKs for PHP, .Net, Ruby, Ruby on Rails, and node.js.

**Originally posted on [Apigee's Best Practices Blog](https://blog.apigee.com/detail/backend_as_a_service_not_just_for_mobile_anymore)**