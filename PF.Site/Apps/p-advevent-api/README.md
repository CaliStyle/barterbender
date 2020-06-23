 
# Advanced Event API

Restful API extension for mobile API

## Restful API document
 
### Event Category Resource API

#### List Event API

*Method:* 
`GET`

*Route:*
`fevent`

*Parameters:*

* `q` - `string`: Search query
* `page` - `int`: Pagination
* `limit` - `int`: Limit number of response
* `category` - `int`: Browse item by category
* `view` - `string`: Filter events, allowed values
    * `(Empty)`: Show all events
    * `my`: Show my events
    * `friend`: Show friend's events
    * `feature`: Show featured events
    * `sponsor`: Show sponsored events
    * `pending`: Show pending events
    * `attending`: Show attending events
    * `may-attend`: Show may attending events
    * `invites`: Show invited events
* `sort` - `string`: sort events
  * `latest`: Sort by latest events
  * `most_viewed`: Sort by most viewed events
  * `most_liked`: Sort by most viewed
  * `most_discussed`: Sort by most discussed
* `when` - `string`: Filter by time, allow values
    * `(Empty)`: Show all events
    * `today`: Show today events
    * `tomorrow`: Show tomorrow events
    * `this-week`: Show events posted this week
    * `this-weekend`: Show events posted this week
    * `this-month`: Show events posted this month
    * `upcoming`: Show upcoming events
    * `ongoing`: Show ongoing events

```json
 {
   "status": "success",
   "data": [
     {
       "resource_name": "fevent",
       "module_name": "fevent",
       "title": "Long upcoming",
       "description": "If you are going to use a passage of Lorem Ipsum",
       "text": null,
       "module_id": "fevent",
       "item_id": "0",
       "view_id": 0,
       "is_sponsor": false,
       "is_featured": false,
       "is_friend": null,
       "is_liked": false,
       "is_pending": false,
       "mass_email": "0",
       "post_types": [
         {
           "value": "post.status",
           "label": "Post",
           "description": "Write something...",
           "icon": "quotes-right",
           "icon_color": "#0f81d8"
         },
         {
           "value": "post.photo",
           "label": "Photo",
           "description": "Say something about this photo...",
           "icon": "photos",
           "icon_color": "#48c260"
         }
       ],
       "profile_menus": [],
       "is_notified": false,
       "image": "http://192.168.15.17/pf47/PF.Base/file/pic/event/2019/05/e53344bd8b99fa3529b99a15e812064c.jpg",
       "images": null,
       "full_address": "dsa",
       "location": "dsa",
       "country": "",
       "province": "",
       "postal_code": null,
       "city": null,
       "start_time_text": "Thu, May 23, 2019 9:34PM",
       "end_time_text": "Fri, May 31, 2019 10:34PM",
       "start_time_date_text": "Thu, May 23, 2019",
       "start_time_time_text": "9:34PM",
       "end_time_date_text": "Fri, May 31, 2019",
       "end_time_time_text": "10:34PM",
       "start_time_date": "2019-05-23",
       "start_time_time": "21:34",
       "end_time_date": "2019-05-31",
       "end_time_time": "22:34",
       "start_time": "2019-05-23T14:34:00+00:00",
       "end_time": "2019-05-31T15:34:00+00:00",
       "start_gmt_offset": "0",
       "end_gmt_offset": "0",
       "gmap": "a:2:{s:8:\"latitude\";s:9:\"36.755656\";s:9:\"longitude\";s:18:\"-95.93931900000001\";}",
       "map_image": null,
       "map_url": null,
       "address": null,
       "country_iso": "",
       "country_child_id": 0,
       "rsvp": -1,
       "statistic": {
         "total_like": 0,
         "total_comment": 0,
         "total_view": 4,
         "total_attachment": 0,
         "total_attending": 1,
         "total_maybe_attending": 0,
         "total_awaiting_reply": 0
       },
       "privacy": 0,
       "user": {
         "resource_name": "user",
         "module_name": "user",
         "full_name": "Asd Queue",
         "avatar": "http://192.168.15.17/pf47/PF.Site/Apps/core-mobile-api/assets/images/default-images/user/no_image.png",
         "is_featured": true,
         "friend_id": 21,
         "id": 2
       },
       "categories": [
         {
           "resource_name": "fevent_category",
           "name": "Comedy",
           "subs": null,
           "id": 3
         }
       ],
       "tags": [],
       "has_ticket": false,
       "ticket_type": "no_ticket",
       "ticket_price": null,
       "ticket_url": "",
       "isrepeat": -1,
       "after_number_event": "0",
       "repeatuntil": null,
       "id": 81,
       "creation_date": "2019-05-04T07:34:58+00:00",
       "modification_date": null,
       "link": "http://192.168.15.17/pf47/index.php/fevent/81/long-upcoming/",
       "extra": {
         "can_view": true,
         "can_like": true,
         "can_share": true,
         "can_delete": true,
         "can_report": true,
         "can_add": true,
         "can_edit": true,
         "can_manage_photo": true,
         "can_comment": true,
         "can_feature": true,
         "can_approve": false,
         "can_sponsor": true,
         "can_sponsor_in_feed": false,
         "can_purchase_sponsor": false,
         "can_invite": true,
         "can_mass_email": false
       }
     }
   ],
   "message": "",
   "error": null
 }
```

*Response Error:*

**Invalid param**

```json
{
    "status": "failed",
    "error": {
        "message": "Following parameters is invalid: category",
        "type": "ValidationErrorException",
        "code": 201,
        "support_message": null,
        "tracer": "23ae121193cf2c490192e064acab3e78",
        "error_data": "...",
        "validation_detail": [
            "category"
        ]
    }
}
```

**Permission denied**

```json
{
    "status": "failed",
    "error": {
        "message": "Permission denied.",
        "type": "PermissionErrorException",
        "code": 301,
        "support_message": "",
        "tracer": "fad795b98075a42efac39daafd5fcc2b",
        "error_data": "..."
    }
}
``` 

#### Get Event Detail

*Method:* 
`GET`

*Route:*
`fevent/:id`

*Parameters:*

* `id` - `int`: Event ID
    
*Response Success:*

```json
{
  "status": "success",
  "data": {
    "resource_name": "fevent",
    "module_name": "fevent",
    "title": "Recur mobile",
    "description": "",
    "text": null,
    "module_id": "fevent",
    "item_id": "0",
    "view_id": 0,
    "is_sponsor": true,
    "is_featured": true,
    "is_friend": false,
    "is_liked": true,
    "is_pending": false,
    "mass_email": "1558410496",
    "post_types": [
      {
        "value": "post.status",
        "label": "Post",
        "description": "Write something...",
        "icon": "quotes-right",
        "icon_color": "#0f81d8"
      },
      {
        "value": "post.photo",
        "label": "Photo",
        "description": "Say something about this photo...",
        "icon": "photos",
        "icon_color": "#48c260"
      }
    ],
    "profile_menus": [],
    "is_notified": false,
    "image": "http://192.168.15.17/pf47/PF.Base/file/pic/event/2019/05/127254c78f67d9909941325b469de2a8.jpeg",
    "images": [
      {
        "50": "http://192.168.15.17/pf47/PF.Base/file/pic/event/2019/05/127254c78f67d9909941325b469de2a8_120_square.jpeg",
        "120": "http://192.168.15.17/pf47/PF.Base/file/pic/event/2019/05/127254c78f67d9909941325b469de2a8_120_square.jpeg",
        "200": "http://192.168.15.17/pf47/PF.Base/file/pic/event/2019/05/127254c78f67d9909941325b469de2a8_200_square.jpeg",
        "image_url": "http://192.168.15.17/pf47/PF.Base/file/pic/event/2019/05/127254c78f67d9909941325b469de2a8.jpeg"
      }
    ],
    "full_address": "Here - Aland Islands",
    "location": "Here",
    "country": "Aland Islands",
    "province": "",
    "postal_code": null,
    "city": null,
    "start_time_text": "Fri, May 17, 2019 2:31PM",
    "end_time_text": "Fri, May 31, 2019 6:31PM",
    "start_time_date_text": "Fri, May 17, 2019",
    "start_time_time_text": "2:31PM",
    "end_time_date_text": "Fri, May 31, 2019",
    "end_time_time_text": "6:31PM",
    "start_time_date": "2019-05-17",
    "start_time_time": "14:31",
    "end_time_date": "2019-05-31",
    "end_time_time": "18:31",
    "start_time": "2019-05-17T07:31:00+00:00",
    "end_time": "2019-05-31T11:31:00+00:00",
    "start_gmt_offset": "7",
    "end_gmt_offset": "7",
    "gmap": {
      "latitude": "",
      "longitude": ""
    },
    "map_image": null,
    "map_url": null,
    "address": null,
    "country_iso": "AX",
    "country_child_id": 0,
    "rsvp": 3,
    "statistic": {
      "total_like": 2,
      "total_comment": 2,
      "total_view": 98,
      "total_attachment": 0,
      "total_attending": 0,
      "total_maybe_attending": 0,
      "total_awaiting_reply": 1
    },
    "privacy": 0,
    "user": {
      "resource_name": "user",
      "module_name": "user",
      "full_name": "Admin",
      "avatar": "http://192.168.15.17/pf47/PF.Base/file/pic/user/2019/04/702f249226c9ed1e6fbc1c4550a2e9ad_200_square.jpg",
      "is_featured": true,
      "friend_id": 0,
      "id": 1
    },
    "categories": [
      {
        "resource_name": "fevent_category",
        "name": "Arts",
        "subs": null,
        "id": 1
      }
    ],
    "tags": [],
    "has_ticket": false,
    "ticket_type": "no_ticket",
    "ticket_price": null,
    "ticket_url": "",
    "isrepeat": 0,
    "after_number_event": "0",
    "repeatuntil": null,
    "id": 114,
    "creation_date": "2019-05-17T07:32:27+00:00",
    "modification_date": null,
    "link": "http://192.168.15.17/pf47/index.php/fevent/114/recur-mobile/",
    "extra": {
      "can_view": true,
      "can_like": true,
      "can_share": true,
      "can_delete": true,
      "can_report": false,
      "can_add": true,
      "can_edit": true,
      "can_manage_photo": true,
      "can_comment": true,
      "can_feature": true,
      "can_approve": false,
      "can_sponsor": true,
      "can_sponsor_in_feed": false,
      "can_purchase_sponsor": false,
      "can_invite": true,
      "can_mass_email": true
    },
    "feed_param": {
      "item_id": 114,
      "comment_type_id": "fevent",
      "total_comment": 2,
      "like_type_id": "fevent",
      "total_like": 2,
      "feed_title": "Recur mobile",
      "feed_link": "http://192.168.15.17/pf47/index.php/fevent/114/recur-mobile/",
      "feed_is_liked": true,
      "feed_is_friend": false,
      "report_module": "fevent",
      "like_phrase": "<span class=\"people-liked-feed\">You and <span class=\"user_profile_link_span\" id=\"js_user_name_link_asdqyop\"><a href=\"http://192.168.15.17/pf47/index.php/asdqyop/\">Asd Queue</a></span></span> like this."
    }
  },
  "message": "",
  "error": null
}
```

*Response Error:*

```json
{
    "status": "failed",
    "error": {
        "message": "Permission denied.",
        "type": "PermissionErrorException",
        "code": 301,
        "support_message": "",
        "tracer": "496e09093454ad0511fe904284974256",
        "error_data": "..."
    }
}
```

```json
{
    "status": "failed",
    "error": {
        "message": "Item not found.",
        "type": "NotFoundErrorException",
        "code": 404,
        "support_message": "",
        "tracer": "1161bf8316547b3e78f93447bc159b46",
        "error_data": "..."
    }
}
```

#### Add Event API

*Method:*
`POST`

*Route:*
`fevent`

*Request*

* `title` - `string`: Event title
* `text` - `mediumtext`: Event content
* `module_id` - `string`: Parent module
* `item_id` - `int`: Parent item
* `categories` - `array`: Categories event belong to
* `file[status]` : "new"
* `file[temp_file]` : temp file ID
* `start_time` - `string`: Start date, format ISO8601 "Y-m-d\TH:i:sO"
* `end_time` - `string`: End time, format ISO8601 "Y-m-d\TH:i:sO"
* `location` - `string`: Event location
* `address` - `string`: Event address
* `city` - `string`: Event city
* `postal_code` - `string`: Event adderss postal code
* `country_state`: `array`: Event country
* `ticket_type` - `string`: Event ticket "no_ticket"/"free"/"paid"
    * `no_ticket` : This event has no ticket
    * `free` : Free ticket
    * `paid` : Paid ticket
* `ticket_price` - `string`: Ticket price
* `ticket_url` - `string`: Ticket link URL
* `isrepeat` - `int`: Repeat type
    * -1 : No repeat
    * 0 : Repeat daily
    * 1 : Repeat weekly
    * 2 : Repeat monthly
* `after_number_event` - `int`: End repeat after n events
* `repeatuntil` - `string`: End repeat date, format YYYY-MM-DD
* `notification_type` - `string`: Notification type and period
    * `no_remind` : Not remind
    * `minute` : Remind in n minutes before event start
    * `hour`  : Remind in n hours before event start
    * `day` : Remind in n days before event start
* `notification_value` - `int`: Notification value
* `privacy`: `int` Event privacy


*Response Success:*

```json
{
  "status": "success",
  "data": {
    "id": 118,
    "resource_name": "fevent"
  },
  "message": "",
  "error": null
}
```

*Response Error:*

**Permission denied**
```json
{
    "status": "failed",
    "error": {
        "message": "Permission denied.",
        "type": "PermissionErrorException",
        "code": 301,
        "support_message": "",
        "tracer": "162a818da79766f7ca33415e3e6a766f",
        "error_data": "..."
    }
}
```

**Invalid Params**

```json
{
    "status": "failed",
    "error": {
        "message": "Following parameters is invalid: Event Name field is invalid, Ticket field is invalid, Start Date field is invalid, Start Time field is invalid, End Date field is invalid, End Time field is invalid, Repeat field is invalid, Location/Venue field is invalid, Featured Photo field is invalid, Notification Reminder field is invalid.",
        "type": "ValidationErrorException",
        "code": 201,
        "support_message": "",
        "tracer": "f2bb3acab8c724f93fa134df9c8d75e9",
        "error_data": "#0 /Users/macpro/Sites/pf47/PF.Site/Apps/p-advevent-api/Service/EventApi.php(519): Apps\\Core_MobileApi\\Service\\AbstractApi->validationParamsError(Array)\n#1 /Users/macpro/Sites/pf47/PF.Src/Core/Api/ApiServiceBase.php(204): Apps\\P_AdvEventAPI\\Service\\EventApi->create(Array)\n#2 /Users/macpro/Sites/pf47/PF.Site/Apps/core-mobile-api/Service/AbstractApi.php(159): Core\\Api\\ApiServiceBase->process(Object(Core\\Route\\RouteUrl), Object(Apps\\phpFox_RESTful_API\\Service\\RestApiTransport), 'create')\n#3 /Users/macpro/Sites/pf47/PF.Site/Apps/core-mobile-api/Service/ApiVersionResolver.php(186): Apps\\Core_MobileApi\\Service\\AbstractApi->process(Object(Core\\Route\\RouteUrl), Object(Apps\\phpFox_RESTful_API\\Service\\RestApiTransport), 'create')\n#4 /Users/macpro/Sites/pf47/PF.Src/Core/Route/Controller.php(342): Apps\\Core_MobileApi\\Service\\ApiVersionResolver->process(Object(Core\\Route\\RouteUrl), Object(Apps\\phpFox_RESTful_API\\Service\\RestApiTransport), 'create')\n#5 /Users/macpro/Sites/pf47/PF.Base/include/library/phpfox/module/module.class.php(355): Core\\Route\\Controller->get()\n#6 /Users/macpro/Sites/pf47/PF.Base/include/library/phpfox/phpfox/phpfox.class.php(1594): Phpfox_Module->setController()\n#7 /Users/macpro/Sites/pf47/PF.Base/start.php(632): Phpfox::run()\n#8 /Users/macpro/Sites/pf47/index.php(5): require('/Users/macpro/S...')\n#9 {main}",
        "validation_detail": {
            "title": "Event Name field is invalid",
            "ticket_type": "Ticket field is invalid",
            "start_time_date": "Start Date field is invalid",
            "start_time_time": "Start Time field is invalid",
            "end_time_date": "End Date field is invalid",
            "end_time_time": "End Time field is invalid",
            "isrepeat": "Repeat field is invalid",
            "location": "Location/Venue field is invalid",
            "file": "Featured Photo field is invalid",
            "notification_type": "Notification Reminder field is invalid"
        }
    }
}
```

**Category id not exist**
```json
{
    "status": "failed",
    "error": {
        "message": "Category not found.",
        "type": "UnknownErrorException",
        "code": 777,
        "support_message": "",
        "tracer": "22f23ca191d7d54adf3089cbfdcfe815",
        "error_data": "..."
    }
}
```


#### Edit Event API

*Method:* 
`PUT`

*Route:*
`fevent/{{id}}`

*Parameters:*

* `ynfevent_editconfirmboxoption_value` - `string`: Select repeat event to edit: "only_this_event"/"following_events"/"all_events_uppercase" 
* `title` - `string`: Event title
* `text` - `mediumtext`: Event content
* `module_id` - `string`: Parent module
* `item_id` - `int`: Parent item
* `categories` - `array`: Categories event belong to
* `file` - `file`: Event photo
* `start_time` - `string`: Start date, format ISO8601 "Y-m-d\TH:i:sO"
* `end_time` - `string`: End time, format ISO8601 "Y-m-d\TH:i:sO"
* `location` - `string`: Event location
* `address` - `string`: Event address
* `city` - `string`: Event city
* `postal_code` - `string`: Event adderss postal code
* `country_state`: `array`: Event country
* `ticket_type` - `string`: Event ticket "no_ticket"/"free"/"paid"
    * `no_ticket` : This event has no ticket
    * `free` : Free ticket
    * `paid` : Paid ticket
* `ticket_price` - `string`: Ticket price
* `ticket_url` - `string`: Ticket link URL
* `isrepeat` - `int`: Repeat type
    * `-1` : No repeat
    * `0` : Repeat daily
    * `1` : Repeat weekly
    * `2` : Repeat monthly
* `after_number_event` - `int`: End repeat after n events
* `repeatuntil` - `string`: End repeat date, format YYYY-MM-DD
* `notification_type` - `string`: Notification type and period
    * `no_remind` : Not remind
    * `minute` : Remind in n minutes before event start
    * `hour`  : Remind in n hours before event start
    * `day` : Remind in n days before event start
* `notification_value` - `int`: Notification value
* `privacy`: `int` Event privacy


*Response Success:*

```json
{
  "status": "success",
  "data": {
    "id": 118,
    "resource_name": "fevent"
  },
  "message": "",
  "error": null
}
```

*Response Error:*

**Permission denied**
```json
{
    "status": "failed",
    "error": {
        "message": "Permission denied.",
        "type": "PermissionErrorException",
        "code": 301,
        "support_message": "",
        "tracer": "162a818da79766f7ca33415e3e6a766f",
        "error_data": "..."
    }
}
```

**Item not found**

```json
{
  "status": "failed",
  "error": {
    "message": "Item not found.",
    "type": "NotFoundErrorException",
    "code": 404,
    "support_message": "",
    "tracer": "c72c159761e7eda1a902238e67759d4d",
    "error_data": "..."
  }
}
```

**Category id not exist**
```json
{
"status": "failed",
    "error": {
        "message": "Category not found.",
        "type": "UnknownErrorException",
        "code": 777,
        "support_message": "",
        "tracer": "22f23ca191d7d54adf3089cbfdcfe815",
        "error_data": "..."
    }
}
```


#### Delete Event API

*Method:*
`DELETE`

*Route:*
`fevent/{{id}}`

*Response Success:*

```json
{
  "status": "success",
  "data": {},
  "message": "Successfully deleted event.",
  "error": null
}
```

*Response Error:*

**Item not found**

```json
{
  "status": "failed",
  "error": {
    "message": "Item not found.",
    "type": "NotFoundErrorException",
    "code": 404,
    "support_message": "",
    "tracer": "c72c159761e7eda1a902238e67759d4d",
    "error_data": "..."
  }
}
```

**Permission denied**

```json
{
  "status": "failed",
  "error": {
    "message": "Permission denied.",
    "type": "PermissionErrorException",
    "code": 301,
    "support_message": "",
    "tracer": "92cef68439393f48f2d2f6d9ff5ac9ec",
    "error_data": "..."
  }
}
```

#### Attend Event API

*Method:*
`PUT`

*Route:*
`fevent/rsvp/{{id}}`

*Parameters:*

* `rsvp` - `int`: Attedning type
    * `1` : Attending
    * `2` : Maybe attending
    * `3` : Not attending

*Response Success:*

```json
{
  "status": "success",
  "data": {
    "rsvp": 1
  },
  "message": "",
  "error": null
}
```

*Response Error:*

**Item not found**

```json
{
    "status": "failed",
    "error": {
        "message": "Item not found.",
        "type": "NotFoundErrorException",
        "code": 404,
        "support_message": "",
        "tracer": "46ae14bc9ac3654431d64db140b3c17f",
        "error_data": "..."
    }
}
```

#### Feature/UnFeature Event API

*Method:*
`PUT`

*Route:*
`fevent/feature/{{id}}`

*Parameters:*

* `feature` - `int`: Feature type
    `1` : Feature event
    `0` : Un-Feature event

*Response Success:*

```json
{
  "status": "success",
  "data": {
    "is_featured": true
  },
  "message": "The event has been featured successfully",
  "error": null
}
```

*Response Error:*

**Item not found**

```json
{
    "status": "failed",
    "error": {
        "message": "Item not found.",
        "type": "NotFoundErrorException",
        "code": 404,
        "support_message": "",
        "tracer": "46ae14bc9ac3654431d64db140b3c17f",
        "error_data": "..."
    }
}
```

**Permission denied**

```json
{
  "status": "failed",
  "error": {
    "message": "Permission denied.",
    "type": "PermissionErrorException",
    "code": 301,
    "support_message": "",
    "tracer": "92cef68439393f48f2d2f6d9ff5ac9ec",
    "error_data": "..."
  }
}
```

#### Sponsor/UnSponsor Event API

*Method:*
`PUT`

*Route:*
`fevent/sponsor/{{id}}`

*Parameters:*

* `sponsor` - `int`: Sponsor type
    * `1` : Sponsor event
    * `0` : Un-Sponsor event

*Response Success:*

```json
{
  "status": "success",
  "data": {
    "is_sponsor": true
  },
  "message": "Event successfully sponsored",
  "error": null
}
```

*Response Error:*

**Item not found**

```json
{
    "status": "failed",
    "error": {
        "message": "Item not found.",
        "type": "NotFoundErrorException",
        "code": 404,
        "support_message": "",
        "tracer": "46ae14bc9ac3654431d64db140b3c17f",
        "error_data": "..."
    }
}
```

**Permission denied**

```json
{
  "status": "failed",
  "error": {
    "message": "Permission denied.",
    "type": "PermissionErrorException",
    "code": 301,
    "support_message": "",
    "tracer": "92cef68439393f48f2d2f6d9ff5ac9ec",
    "error_data": "..."
  }
}
```


#### Approve Event API

*Method:*
`PUT`

*Route:*
`fevent/approve/{{id}}`

*Response Success:*

```json
{
  "status": "success",
  "data": {
    "can_view": true,
    "can_like": false,
    "can_share": false,
    "can_delete": true,
    "can_report": true,
    "can_add": true,
    "can_edit": true,
    "can_manage_photo": true,
    "can_comment": true,
    "can_feature": true,
    "can_approve": true,
    "can_sponsor": true,
    "can_sponsor_in_feed": false,
    "can_purchase_sponsor": false,
    "can_invite": false,
    "can_mass_email": false,
    "is_pending": false
  },
  "message": "Event has been approved.",
  "error": null
}
```

*Response Error:*

**Item not found**

```json
{
    "status": "failed",
    "error": {
        "message": "Item not found.",
        "type": "NotFoundErrorException",
        "code": 404,
        "support_message": "",
        "tracer": "46ae14bc9ac3654431d64db140b3c17f",
        "error_data": "..."
    }
}
```

**Permission denied**

```json
{
  "status": "failed",
  "error": {
    "message": "Permission denied.",
    "type": "PermissionErrorException",
    "code": 301,
    "support_message": "",
    "tracer": "92cef68439393f48f2d2f6d9ff5ac9ec",
    "error_data": "..."
  }
}
```

#### Send Mass Email API

*Method:*
`POST`

*Route:*
`fevent/mass-email`

*Parameters:*

* `event_id` - `int`: Event ID
* `subject` - `string`: Email Subject
* `text` - `string`: Email Content

*Response Success:*

```json
{
  "status": "success",
  "data": {},
  "message": "Done",
  "error": null
}
```

*Response Error:*
**Item not found**

```json
{
    "status": "failed",
    "error": {
        "message": "Item not found.",
        "type": "NotFoundErrorException",
        "code": 404,
        "support_message": "",
        "tracer": "46ae14bc9ac3654431d64db140b3c17f",
        "error_data": "..."
    }
}
```

**Permission denied**

```json
{
  "status": "failed",
  "error": {
    "message": "Permission denied.",
    "type": "PermissionErrorException",
    "code": 301,
    "support_message": "",
    "tracer": "92cef68439393f48f2d2f6d9ff5ac9ec",
    "error_data": "..."
  }
}
```

### Event Category Resource API

#### List Category API

*Method:* 
`GET`

*Route:*
`fevent-category`

*Response Success:*

```json
{
    "status": "success",
    "data": [
        {
            "resource_name": "fevent_category",
            "name": "Arts",
            "subs": [
                {
                    "resource_name": "fevent_category",
                    "name": "Painting",
                    "subs": null,
                    "id": 9
                }
            ],
            "id": 1
        }
    ],
    "message": "",
    "error": null
}
```

*Response Error:*

**Permission denied**
```json
{
    "status": "failed",
    "error": {
        "message": "Permission denied.",
        "type": "PermissionErrorException",
        "code": 301,
        "support_message": "",
        "tracer": "2fd1420d7bd631367e920c7a0f8c49d4"
    }
}
```

### Event Invite Resource API

#### Event Guest List API

*Method:* 
`GET`

*Route:*
`fevent-invite`

*Parameters:*

* `event_id` - `int`: Event ID
* `rsvp_id` - `int`: Attending type
    * `0` : Awaiting
    * `1` : Attending
    * `2` : Maybe attending
    * `3` : Not attending
* `page` - `int`: Pagination
* `limit` - `int`: Limit number of response

*Response Success:*

```json
{
  "status": "success",
  "data": [
    {
      "resource_name": "fevent_invite",
      "event_id": 118,
      "type_id": 0,
      "rsvp_id": 1,
      "invited_email": null,
      "user": {
        "resource_name": "user",
        "module_name": "user",
        "full_name": "Admin",
        "avatar": "http://192.168.15.17/pf47/PF.Base/file/pic/user/2019/04/702f249226c9ed1e6fbc1c4550a2e9ad_200_square.jpg",
        "is_featured": true,
        "friend_id": 0,
        "id": 1
      },
      "module_name": null,
      "id": 125,
      "creation_date": "2019-05-23T07:04:35+00:00",
      "modification_date": null,
      "link": "http://192.168.15.17/pf47/index.php/fevent/118/",
      "extra": null,
      "privacy": 0
    }
  ],
  "message": "",
  "error": null
}
```

*Response Error:*

**Item not found**

```json
{
    "status": "failed",
    "error": {
        "message": "Item not found.",
        "type": "NotFoundErrorException",
        "code": 404,
        "support_message": "",
        "tracer": "46ae14bc9ac3654431d64db140b3c17f",
        "error_data": "..."
    }
}
```

**Permission denied**

```json
{
  "status": "failed",
  "error": {
    "message": "Permission denied.",
    "type": "PermissionErrorException",
    "code": 301,
    "support_message": "",
    "tracer": "92cef68439393f48f2d2f6d9ff5ac9ec",
    "error_data": "..."
  }
}
```

#### Send Invite API

*Method:* 
`POST`

*Route:*
`fevent-invite`

*Parameters:*

* `event_id` - `int`: Event ID
* `user_ids` - `array`: List of invited user Ids
* `emails` - `string`: List of emails, comma delimited
* `personal_message` - `string`: Message to send to invited user

*Response Success:*

```json
{
  "status": "success",
  "data": {
    "id": "118",
    "resource_name": "fevent"
  },
  "message": "Invitation(s) successfully sent.",
  "error": null
}
```

*Response Error:*

**Item not found**

```json
{
    "status": "failed",
    "error": {
        "message": "Item not found.",
        "type": "NotFoundErrorException",
        "code": 404,
        "support_message": "",
        "tracer": "46ae14bc9ac3654431d64db140b3c17f",
        "error_data": "..."
    }
}
```

**Permission denied**

```json
{
  "status": "failed",
  "error": {
    "message": "Permission denied.",
    "type": "PermissionErrorException",
    "code": 301,
    "support_message": "",
    "tracer": "92cef68439393f48f2d2f6d9ff5ac9ec",
    "error_data": "..."
  }
}
```

### Event Admin Resource API

#### Event Admin List API

*Method:* 
`GET`

*Route:*
`fevent-admin`

*Parameters:*

* `id` - `int`: Event ID
* `page` - `int`: Pagination
* `limit` - `int`: Limit number of response

*Response Success:*

```json
{
  "status": "success",
  "data": [
    {
      "resource_name": "fevent_admin",
      "full_name": "John Doe",
      "avatar": null,
      "user_id": 2,
      "event_id": 1,
      "is_featured": true,
      "id": "118:2"
    }
  ],
  "message": "",
  "error": null
}
```

*Response Error:*

**Item not found**

```json
{
    "status": "failed",
    "error": {
        "message": "Item not found.",
        "type": "NotFoundErrorException",
        "code": 404,
        "support_message": "",
        "tracer": "46ae14bc9ac3654431d64db140b3c17f",
        "error_data": "..."
    }
}
```

**Permission denied**

```json
{
  "status": "failed",
  "error": {
    "message": "Permission denied.",
    "type": "PermissionErrorException",
    "code": 301,
    "support_message": "",
    "tracer": "92cef68439393f48f2d2f6d9ff5ac9ec",
    "error_data": "..."
  }
}
```

#### Remove Admin API

*Method:* 
`DELETE`

*Route:*
`fevent-admin`

*Parameters:*

* `id` - `int`: Event ID
* `user_id` - `int`: Admin user ID that will be removed

*Response Success:*

```json
{
  "status": "success",
  "data": {},
  "message": "The event has been updated successfully",
  "error": null
}
```

*Response Error:*

**Item not found**

```json
{
    "status": "failed",
    "error": {
        "message": "Item not found.",
        "type": "NotFoundErrorException",
        "code": 404,
        "support_message": "",
        "tracer": "46ae14bc9ac3654431d64db140b3c17f",
        "error_data": "..."
    }
}
```

**Permission denied**

```json
{
  "status": "failed",
  "error": {
    "message": "Permission denied.",
    "type": "PermissionErrorException",
    "code": 301,
    "support_message": "",
    "tracer": "92cef68439393f48f2d2f6d9ff5ac9ec",
    "error_data": "..."
  }
}
```

#### Update Event Admin List API

*Method:* 
`PUT`

*Route:*
`fevent-admin/{{id}}`

*Parameters:*

* `id` - `int`: Event ID
* `user_ids` - `array`: List of admin user ID that will be added

*Response Success:*

```json
{
  "status": "success",
  "data": {
    "id": 1
  },
  "message": "The event has been updated successfully",
  "error": null
}
```

*Response Error:*

**Item not found**

```json
{
    "status": "failed",
    "error": {
        "message": "Item not found.",
        "type": "NotFoundErrorException",
        "code": 404,
        "support_message": "",
        "tracer": "46ae14bc9ac3654431d64db140b3c17f",
        "error_data": "..."
    }
}
```

**Permission denied**

```json
{
  "status": "failed",
  "error": {
    "message": "Permission denied.",
    "type": "PermissionErrorException",
    "code": 301,
    "support_message": "",
    "tracer": "92cef68439393f48f2d2f6d9ff5ac9ec",
    "error_data": "..."
  }
}
```
