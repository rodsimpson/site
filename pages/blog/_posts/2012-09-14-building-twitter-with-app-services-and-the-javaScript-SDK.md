---
layout: post
headline: Building Twitter with App Services and the JavaScript SDK
title: Building Twitter with App Services and the JavaScript SDK
keywords: Rod Simpson, BaaS, Apigee, APIs
description: Rod Simpson - Building Twitter with App Services and the JavaScript SDK
date: 2012-09-14 17:00:00 -06:00
image: /images/blog/sf-park.jpg
credit: San Francisco Park, <a href="http://rodsimpson.com">Rod Simpson</a> copyright 2012
cats:
  - Tech Stuff
  - Software
tags:
  - BaaS
  - Software
  - APIs
---

Twitter seems like a very simple app on the surface. 140 characters, users following users - building an app like that should be easy, right?  It is - until you start to look under the hood.  A quick examination reveals that there are a number of fairly complex features that need to be implemented on the back end: user management, activity streams, support for connections (users following users), and all of this packaged up in a nice, easy-to-use API.

We recently used Apigee App Services to quickly build out our Twitter clone called Messagee using JavaScript, HTML5, and the Apigee JavaScript SDK. This post summarizes the steps we took. Also, check out the Source Code for our Messagee app, the Working Demo, and the Tutorial, which describes in detail what we did.

##What we Built
Our "Twitter clone" app allows users to post 140 character messages, or “tweets” to the system.  Users can also become “followers” of other users.  When users log in to check their messages, they can see either a general feed of all messages posted to the system or a personalized message “feed”. This personalized feed consists of messages the user has posted as well as messages from users they follow.

##The Journey in a Nutshell

Calls to invoke the App Services API (a RESTful API) are simple to use, and because they take and return JSON, they work nicely with JavaScript. The starting point for building HTML5 apps with the App Services API is the JavaScript SDK. The SDK allows us to work with the App Services API by providing the infrastructure that makes the calls to the server, including encoding and decoding JSON.

##User management:
We needed to build the forms to support user management features and the simple functions that handle user interaction with those forms. We built a new user creation form, a form to update a user’s account settings, and a login form. The details of the code for the forms is available in the tutorial.

Message feeds are a key feature of Twitter. These feeds (or activity streams) turn individual tweets into user feeds, bridging the gap between a simple message and an interactive social messaging application. Building Activity Streams is done with App Services via simple API calls.

##Creating new activities:
The foundation of activity streams is the activities collection. We use these activities as the containers for messages that are generated in the Messagee app. When a user posts a new message (or tweet), we simply add that message to an activity object and send it to the API.

We post to the activity to /users/currentuser/activities.  This means that, along with creating the activity and adding it to the activities collection, we are  establishing a relationship between the user and the activity they are creating.

##Making connections:
App Services has the built-in facility for users to follow other users.  We take advatage of it in Messagee by adding a “follow” link to each user’s posted message in the display. Clicking the link calls the followUser function, which first gets a reference to the currently logged in user, then makes the API call to create the relationship.

##All and personalized feeds:
To provide an unfiltered activity stream that shows all the activities (messages) posted to the application, we were able to simply query the current user’s activity collection (GET users/currentuser/activities). We also take advantage of the App Services built-in “feed” feature to build personalized feeds.  When a user’s feed is queried (GET users/currentuser/feed), the API dynamically builds the feed based on any relevant relationships and returns the stream of messages.

##In Summary
Taking advantage of the App Services API and the JavaScript SDK, we were able to build a Twitter style app very quickly. We were able to concentrate on the front-end app experience and didn't have to build out any server-side code.

[Source Code](https://github.com/apigee/usergrid-sample-html5-messagee) | [Working Demo](http://apigee.github.io/usergrid-sample-html5-messagee/) | [Tutorial](http://apigee.com/about/api-best-practices/building-twitter-apigee-app-services)

**Originally posted on [Apigee's Best Practices Blog](https://blog.apigee.com/detail/building_twitter_with_app_services_and_the_javascript_sdk)**