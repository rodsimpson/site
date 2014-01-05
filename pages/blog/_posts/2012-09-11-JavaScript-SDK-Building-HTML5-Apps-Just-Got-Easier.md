---
layout: post
headline: JavaScript SDK - Building HTML5 Apps Just Got Easier
title: JavaScript SDK - Building HTML5 Apps Just Got Easier
keywords: Rod Simpson, BaaS, Apigee, APIs
description: Rod Simpson - JavaScript SDK - Building HTML5 Apps Just Got Easier
date: 2012-09-11 17:00:00 -06:00
image: /images/blog/flatirons.jpg
credit: Boulder Flatirons, <a href="http://rodsimpson.com">Rod Simpson</a> copyright 2012
cats:
  - Tech Stuff
  - Software
tags:
  - BaaS
  - Software
  - APIs
---

##Getting Started
When we set out to create a new JavaScript SDK, we wanted a library that has no dependencies on third party libraries, is easy to use and useful across a variety of JavaScript frameworks. It also needed to work with existing technologies like Trigger.io or Phonegap, which enable you to easily package your web app as a native app for distribution on multiple platforms.

##Lots of JavaScript frameworks
On surveying the JavaScript landscape, we found a myriad of frameworks and libraries such as Backbone, Ember, and Spine, and we didn’t see a clear preference among developers. Each of the popular frameworks has different methodologies (MV*, MVC, etc.), different amounts of structure they supply for developers, as well as different amounts of process they impose on apps. Addy Osmani does an excellent job of summarizing the various frameworks in this Smashing Magazine article.

Some libraries like Backbone are very liberal in their demands.  They provide a very basic scaffolding around which to build your app.  However, you are on your own when it comes to developing much of the higher level code.  This isn’t necessarily good or bad - just one philosophy on what a framework should provide.

A framework like Ember provides a more comprehensive solution. Developers get everything they need to craft an application from top to bottom. The trade off is flexibility - although you don’t need to write a lot of boilerplate code, you must be willing to buy into the framework’s methodology.  Again, this is neither good nor bad just a different philosophy.

##Segmentation in the framework marketplace
Given the segmentation in the framework marketplace, we needed to craft something that could be adapted for use with any framework. To achieve this flexibility, we decided to build the SDK using vanilla JavaScript.  This also allowed us to eliminate dependencies on third party libraries. (We love jQuery, but maybe you don’t.) We went with basic JavaScript prototypes and focused on crafting all the code that talks to the API.

##Ease of use
To achieve our goal of creating a library that’s easy to use, we abstracted the most frequently used features of the API - namely Entities and Collections - into simple objects that can be used in any app. Using these data structures in your app should be a natural extension of the API.

##Portability
The HTML/JavaScript platform is currently enjoying a renaissance in the mobile world.  It has turned into a write once, run anywhere platform.  With different style sheets, the same basic code can take on many roles: a traditional website, a mobile web app (essentially a traditional website, but styled for mobile and viewed in a browser), or using a technology like Trigger.io or Phonegap, a native app on any of dozens of mobile platforms. By not having dependencies, the SDK offers excellent performance on all these platforms.

## Getting started
Download the JavaScript SDK from our [github repo](https://github.com/apigee/usergrid-javascript-sdk). It includes an easy-to-follow example.


**Originally posted on [Apigee's Best Practices Blog](https://blog.apigee.com/detail/javascript_sdk_building_html5_apps_just_got_easier)**