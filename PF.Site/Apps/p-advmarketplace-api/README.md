#### List Advanced Marketplace API
*Method:* 
`GET`

*Route:*
`advancedmarketplace`

*Parameters:*

* `q` - `string`: Search query
* `page` - `int`: Pagination
* `limit` - `int`: Limit number of response
* `category` - `int`: Browse items by category
* `view` - `string`: Filter listings, allowed values
    * `(Empty)`: Show all listings
    * `my`: Show my listings
    * `my-wishlist`: Show My Wish list
    * `invites`: Show listing invites
    * `friend`: Show friend's listings
    * `pending`: Show pending listings
    * `expired`: Show favorite listings
* `sort` - `string`: sort videos
  * `latest`: Sort by latest post time
  * `most_viewed`: Sort by most viewd
  * `most_liked`: Sort by most liked
  * `most_discussed`: Sort by most commented
  * `low_high_price`: Sort by price low to high
  * `high_low_price`: Sort by price high to low
* `when` - `string`: Filter by time, allow values
    * `all-time`:
    * `today`:
    * `this-week`:
    * `this-month`:
    * `featured`:
    * `sponsored`:
    
*Response Success:*

```json
{
    "status": "success",
    "data": [
        {
            "resource_name": "advancedmarketplace",
            "module_name": "advancedmarketplace",
            "title": "adv listing in page",
            "description": "",
            "short_description": "",
            "text": null,
            "view_id": 0,
            "is_sponsor": false,
            "is_featured": false,
            "is_friend": null,
            "is_liked": false,
            "is_sell": 0,
            "is_closed": 0,
            "is_expired": null,
            "is_notified": false,
            "is_pending": false,
            "auto_sell": 0,
            "currency_id": "USD",
            "price": "Free",
            "location": null,
            "address": null,
            "payment_methods": [],
            "expired_date": "",
            "mark_sold": false,
            "is_draft": false,
            "image": {
                "50": "http://192.168.15.18/pf47/PF.Base/file/pic/advancedmarketplace/2019/04/ede7fc1b4fe3b7623dd7c58aa629b5fa_120_square.jpg",
                "120": "http://192.168.15.18/pf47/PF.Base/file/pic/advancedmarketplace/2019/04/ede7fc1b4fe3b7623dd7c58aa629b5fa_120_square.jpg",
                "200": "http://192.168.15.18/pf47/PF.Base/file/pic/advancedmarketplace/2019/04/ede7fc1b4fe3b7623dd7c58aa629b5fa_200_square.jpg",
                "400": "http://192.168.15.18/pf47/PF.Base/file/pic/advancedmarketplace/2019/04/ede7fc1b4fe3b7623dd7c58aa629b5fa_400_square.jpg",
                "image_url": "http://192.168.15.18/pf47/PF.Base/file/pic/advancedmarketplace/2019/04/ede7fc1b4fe3b7623dd7c58aa629b5fa.jpg"
            },
            "images": null,
            "group_id": 0,
            "country": "Aland Islands",
            "province": "",
            "postal_code": null,
            "city": null,
            "country_iso": "AX",
            "country_child_id": 0,
            "statistic": {
                "total_like": 0,
                "total_comment": 0,
                "total_view": 2
            },
            "privacy": 0,
            "user": {
                "resource_name": "user",
                "module_name": "user",
                "full_name": "Admin",
                "avatar": "http://192.168.15.18/pf47/PF.Base/file/pic/user/2019/04/702f249226c9ed1e6fbc1c4550a2e9ad_200_square.jpg",
                "id": 1
            },
            "categories": [
                {
                    "resource_name": "advancedmarketplace_category",
                    "name": "Others",
                    "subs": null,
                    "id": 10
                }
            ],
            "tags": [],
            "buy_now_link": null,
            "time_stamp": "1555931086",
            "id": 32,
            "creation_date": "2019-04-22T11:04:46+00:00",
            "modification_date": null,
            "link": "http://192.168.15.18/pf47/index.php/advancedmarketplace/32/adv-listing-in-page/",
            "extra": {
                "can_view": true,
                "can_like": true,
                "can_share": true,
                "can_delete": true,
                "can_report": false,
                "can_add": true,
                "can_edit": true,
                "can_comment": true,
                "can_view_expired": true,
                "can_reopen": false,
                "can_feature": true,
                "can_approve": false,
                "can_sponsor": false,
                "can_sponsor_in_feed": false,
                "can_purchase_sponsor": false,
                "can_invite": true,
                "can_manage_photo": true,
                "can_buy_now": false
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
        "message": "Following parameters is invalid: category.",
        "type": "ValidationErrorException",
        "code": 201,
        "support_message": "",
        "tracer": "4b4cda361d7367e26ff2fc14d12332f4",
        "error_data": "#0 /Users/macpro/Sites/pf47/PF.Site/Apps/p-advmarketplace-api/Service/MarketplaceApi.php(129): Apps\\Core_MobileApi\\Service\\AbstractApi->validationParamsError(Array)\n#1 /Users/macpro/Sites/pf47/PF.Src/Core/Api/ApiServiceBase.php(204): Apps\\P_AdvMarketplaceAPI\\Service\\MarketplaceApi->findAll(Array)\n#2 /Users/macpro/Sites/pf47/PF.Site/Apps/core-mobile-api/Service/AbstractApi.php(159): Core\\Api\\ApiServiceBase->process(Object(Core\\Route\\RouteUrl), Object(Apps\\phpFox_RESTful_API\\Service\\RestApiTransport), 'findAll')\n#3 /Users/macpro/Sites/pf47/PF.Site/Apps/core-mobile-api/Service/ApiVersionResolver.php(186): Apps\\Core_MobileApi\\Service\\AbstractApi->process(Object(Core\\Route\\RouteUrl), Object(Apps\\phpFox_RESTful_API\\Service\\RestApiTransport), 'findAll')\n#4 /Users/macpro/Sites/pf47/PF.Src/Core/Route/Controller.php(342): Apps\\Core_MobileApi\\Service\\ApiVersionResolver->process(Object(Core\\Route\\RouteUrl), Object(Apps\\phpFox_RESTful_API\\Service\\RestApiTransport), 'findAll')\n#5 /Users/macpro/Sites/pf47/PF.Base/include/library/phpfox/module/module.class.php(355): Core\\Route\\Controller->get()\n#6 /Users/macpro/Sites/pf47/PF.Base/include/library/phpfox/phpfox/phpfox.class.php(1594): Phpfox_Module->setController()\n#7 /Users/macpro/Sites/pf47/PF.Base/start.php(631): Phpfox::run()\n#8 /Users/macpro/Sites/pf47/index.php(5): require('/Users/macpro/S...')\n#9 {main}",
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
        "error_data": "#0 /var/www/html/PF.Site/Apps/p-advmarketplace-api/Service/MarketplaceApi.php(106): Apps\\Core_MobileApi\\Service\\AbstractApi->permissionError()\n#1 /var/www/html/PF.Src/Core/Api/ApiServiceBase.php(204): Apps\\P_AdvMarketplaceAPI\\Service\\MarketplaceApi->findAll(Array)\n#2 /var/www/html/PF.Site/Apps/core-mobile-api/Service/AbstractApi.php(159): Core\\Api\\ApiServiceBase->process(Object(Core\\Route\\RouteUrl), Object(Apps\\phpFox_RESTful_API\\Service\\RestApiTransport), 'findAll')\n#3 /var/www/html/PF.Src/Core/Route/Controller.php(342): Apps\\Core_MobileApi\\Service\\AbstractApi->process(Object(Core\\Route\\RouteUrl), Object(Apps\\phpFox_RESTful_API\\Service\\RestApiTransport), 'get')\n#4 /var/www/html/PF.Base/include/library/phpfox/module/module.class.php(356): Core\\Route\\Controller->get()\n#5 /var/www/html/PF.Base/include/library/phpfox/phpfox/phpfox.class.php(1583): Phpfox_Module->setController()\n#6 /var/www/html/PF.Base/start.php(631): Phpfox::run()\n#7 /var/www/html/index.php(5): require('/var/www/html/P...')\n#8 {main}"
    }
}
```

### Advanced Marketplace Category Resource API

#### List Category API
*Method:* 
`GET`

*Route:*
`advancedmarketplace-category`

*Response Success:*

```json
{
    "status": "success",
    "data": [
        {
            "resource_name": "advancedmarketplace_category",
            "name": "Community",
            "subs": [
                {
                    "resource_name": "advancedmarketplace_category",
                    "name": "Community Sub",
                    "subs": null,
                    "id": 11
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

#### Send 
*Method:* 
`POST`

*Request Type:* `application/json`

*Parameters:*

* `listing_id` - `int`: Listing ID
* `user_ids` - `array`: List user id will be sent invites
* `emails` - `string`: List email will be sent invites
* `personal_message` - `string`: Invite message

*Request Example*

```json
{
    "listing_id": 3,
    "user_ids": [
        1,
        2,
        3,
        4,
        5
    ],
    "emails": "abc@mail.com, def@mail.com",
    "personal_message": "Welcome to my listing"
}
```

*Route:*
`advancedmarketplace-invite`

*Response Success:*

```json
{
    "status": "success",
    "data": {
        "id": "1",
        "resource_name": "advancedmarketplace"
    },
    "message": "Invitation(s) successfully sent.",
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
        "tracer": "fe13eacd0c88bbea751cf9c8b98a3ecb",
        "error_data": "#0 /Users/macpro/Sites/pf47/PF.Site/Apps/core-mobile-api/Service/AbstractApi.php(319): Apps\\Core_MobileApi\\Service\\AbstractApi->permissionError('')\n#1 /Users/macpro/Sites/pf47/PF.Site/Apps/p-advmarketplace-api/Service/MarketplaceInviteApi.php(266): Apps\\Core_MobileApi\\Service\\AbstractApi->denyAccessUnlessGranted('asd', Object(Apps\\P_AdvMarketplaceAPI\\Api\\Resource\\MarketplaceResource))\n#2 /Users/macpro/Sites/pf47/PF.Src/Core/Api/ApiServiceBase.php(204): Apps\\P_AdvMarketplaceAPI\\Service\\MarketplaceInviteApi->create(Array)\n#3 /Users/macpro/Sites/pf47/PF.Site/Apps/core-mobile-api/Service/AbstractApi.php(159): Core\\Api\\ApiServiceBase->process(Object(Core\\Route\\RouteUrl), Object(Apps\\phpFox_RESTful_API\\Service\\RestApiTransport), 'create')\n#4 /Users/macpro/Sites/pf47/PF.Site/Apps/core-mobile-api/Service/ApiVersionResolver.php(186): Apps\\Core_MobileApi\\Service\\AbstractApi->process(Object(Core\\Route\\RouteUrl), Object(Apps\\phpFox_RESTful_API\\Service\\RestApiTransport), 'create')\n#5 /Users/macpro/Sites/pf47/PF.Src/Core/Route/Controller.php(342): Apps\\Core_MobileApi\\Service\\ApiVersionResolver->process(Object(Core\\Route\\RouteUrl), Object(Apps\\phpFox_RESTful_API\\Service\\RestApiTransport), 'create')\n#6 /Users/macpro/Sites/pf47/PF.Base/include/library/phpfox/module/module.class.php(355): Core\\Route\\Controller->get()\n#7 /Users/macpro/Sites/pf47/PF.Base/include/library/phpfox/phpfox/phpfox.class.php(1594): Phpfox_Module->setController()\n#8 /Users/macpro/Sites/pf47/PF.Base/start.php(631): Phpfox::run()\n#9 /Users/macpro/Sites/pf47/index.php(5): require('/Users/macpro/S...')\n#10 {main}"
    }
}
```
**Invalid param**

```json
{
    "status": "failed",
    "error": {
        "message": "Item not found.",
        "type": "NotFoundErrorException",
        "code": 404,
        "support_message": "",
        "tracer": "1e13b1b8b30fb9fc06f7e63088205abf",
        "error_data": "#0 /Users/macpro/Sites/pf47/PF.Site/Apps/p-advmarketplace-api/Service/MarketplaceInviteApi.php(264): Apps\\Core_MobileApi\\Service\\AbstractApi->notFoundError()\n#1 /Users/macpro/Sites/pf47/PF.Src/Core/Api/ApiServiceBase.php(204): Apps\\P_AdvMarketplaceAPI\\Service\\MarketplaceInviteApi->create(Array)\n#2 /Users/macpro/Sites/pf47/PF.Site/Apps/core-mobile-api/Service/AbstractApi.php(159): Core\\Api\\ApiServiceBase->process(Object(Core\\Route\\RouteUrl), Object(Apps\\phpFox_RESTful_API\\Service\\RestApiTransport), 'create')\n#3 /Users/macpro/Sites/pf47/PF.Site/Apps/core-mobile-api/Service/ApiVersionResolver.php(186): Apps\\Core_MobileApi\\Service\\AbstractApi->process(Object(Core\\Route\\RouteUrl), Object(Apps\\phpFox_RESTful_API\\Service\\RestApiTransport), 'create')\n#4 /Users/macpro/Sites/pf47/PF.Src/Core/Route/Controller.php(342): Apps\\Core_MobileApi\\Service\\ApiVersionResolver->process(Object(Core\\Route\\RouteUrl), Object(Apps\\phpFox_RESTful_API\\Service\\RestApiTransport), 'create')\n#5 /Users/macpro/Sites/pf47/PF.Base/include/library/phpfox/module/module.class.php(355): Core\\Route\\Controller->get()\n#6 /Users/macpro/Sites/pf47/PF.Base/include/library/phpfox/phpfox/phpfox.class.php(1594): Phpfox_Module->setController()\n#7 /Users/macpro/Sites/pf47/PF.Base/start.php(631): Phpfox::run()\n#8 /Users/macpro/Sites/pf47/index.php(5): require('/Users/macpro/S...')\n#9 {main}"
    }
}
```
#### Add Marketplace API
*Method:* 
`POST`

*Request Type:*
`application/json`

*Route:*
`advancedmarketplace`

*Request:*
```
{
  "categories": [
    1
  ],
  "currency_id": "USD",
  "payment_methods": [
    "paypal",
    "activitypoints"
  ],
  "country_state": [
    "AF"
  ],
  "expired_date": "2019-05-07",
  "privacy": 0,
  "module_id": "advancedmarketplace",
  "item_id": 0,
  "title": "Create advmarketplace",
  "tags": "Test",
  "price": "111",
  "short_description": "Test",
  "description": "Test",
  "location": "Ho chi minh",
  "address": "1953",
  "city": "Ho chi minh",
  "postal_code": "700000",
  "image": {
    "status": "new",
    "temp_file": 194
  },
  "is_sell": 1
}
```
    
*Response Success:*
```json
{
  "status": "success",
  "data": {
    "id": 12,
    "resource_name": "advancedmarketplace"
  },
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
        "message": "Following parameters is invalid: Privacy field is invalid",
        "type": "ValidationErrorException",
        "code": 201,
        "support_message": "",
        "tracer": "4871a179e70e1ed20923b23c111863ab",
        "error_data": "#0 /var/www/html/475/PF.Site/Apps/p-advmarketplace-api/Service/MarketplaceApi.php(461): Apps\\Core_MobileApi\\Service\\AbstractApi->validationParamsError(Array)\n#1 /var/www/html/475/PF.Src/Core/Api/ApiServiceBase.php(204): Apps\\P_AdvMarketplaceAPI\\Service\\MarketplaceApi->update(Array)\n#2 /var/www/html/475/PF.Site/Apps/core-mobile-api/Service/AbstractApi.php(159): Core\\Api\\ApiServiceBase->process(Object(Core\\Route\\RouteUrl), Object(Apps\\phpFox_RESTful_API\\Service\\RestApiTransport), 'update')\n#3 /var/www/html/475/PF.Site/Apps/core-mobile-api/Service/ApiVersionResolver.php(186): Apps\\Core_MobileApi\\Service\\AbstractApi->process(Object(Core\\Route\\RouteUrl), Object(Apps\\phpFox_RESTful_API\\Service\\RestApiTransport), 'update')\n#4 /var/www/html/475/PF.Src/Core/Route/Controller.php(342): Apps\\Core_MobileApi\\Service\\ApiVersionResolver->process(Object(Core\\Route\\RouteUrl), Object(Apps\\phpFox_RESTful_API\\Service\\RestApiTransport), 'update')\n#5 /var/www/html/475/PF.Base/include/library/phpfox/module/module.class.php(355): Core\\Route\\Controller->get()\n#6 /var/www/html/475/PF.Base/include/library/phpfox/phpfox/phpfox.class.php(1594): Phpfox_Module->setController()\n#7 /var/www/html/475/PF.Base/start.php(631): Phpfox::run()\n#8 /var/www/html/475/index.php(5): require('/var/www/html/4...')\n#9 {main}",
        "validation_detail": {
            "privacy": "Privacy field is invalid"
        }
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
        "tracer": "6639c004dc2bc4d3c0447ae61f122670",
        "error_data": "#0 /var/www/html/475/PF.Site/Apps/core-mobile-api/Service/AbstractApi.php(319): Apps\\Core_MobileApi\\Service\\AbstractApi->permissionError('')\n#1 /var/www/html/475/PF.Site/Apps/p-advmarketplace-api/Service/MarketplaceApi.php(444): Apps\\Core_MobileApi\\Service\\AbstractApi->denyAccessUnlessGranted('edit', Object(Apps\\P_AdvMarketplaceAPI\\Api\\Resource\\MarketplaceResource))\n#2 /var/www/html/475/PF.Src/Core/Api/ApiServiceBase.php(204): Apps\\P_AdvMarketplaceAPI\\Service\\MarketplaceApi->update(Array)\n#3 /var/www/html/475/PF.Site/Apps/core-mobile-api/Service/AbstractApi.php(159): Core\\Api\\ApiServiceBase->process(Object(Core\\Route\\RouteUrl), Object(Apps\\phpFox_RESTful_API\\Service\\RestApiTransport), 'update')\n#4 /var/www/html/475/PF.Site/Apps/core-mobile-api/Service/ApiVersionResolver.php(186): Apps\\Core_MobileApi\\Service\\AbstractApi->process(Object(Core\\Route\\RouteUrl), Object(Apps\\phpFox_RESTful_API\\Service\\RestApiTransport), 'update')\n#5 /var/www/html/475/PF.Src/Core/Route/Controller.php(342): Apps\\Core_MobileApi\\Service\\ApiVersionResolver->process(Object(Core\\Route\\RouteUrl), Object(Apps\\phpFox_RESTful_API\\Service\\RestApiTransport), 'update')\n#6 /var/www/html/475/PF.Base/include/library/phpfox/module/module.class.php(355): Core\\Route\\Controller->get()\n#7 /var/www/html/475/PF.Base/include/library/phpfox/phpfox/phpfox.class.php(1594): Phpfox_Module->setController()\n#8 /var/www/html/475/PF.Base/start.php(631): Phpfox::run()\n#9 /var/www/html/475/index.php(5): require('/var/www/html/4...')\n#10 {main}"
    }
}
```

#### Edit Marketplace API
*Method:* 
`PUT`

*Request Type:*
`application/json`

*Route:*
`advancedmarketplace/{{id}}`

*Request:*

```json
{
	"title":"Edit Marketplace",
    "description":"Marketplace description",
    "short_description" : "Marketplace short description",
    "privacy":"0",
    "tags":"tag1, tag2",
    "categories":[1],
    "city" : "Ho Chi Minh",
    "currency_id" : "USD",
    "country_iso" : "US"
}
```
    
*Response Success:*

```json
{
    "status": "success",
    "data": {
        "id": "4",
        "resource_name": "advancedmarketplace"
    },
    "message": "Listing successfully updated.",
    "error": null
}
```
*Response Error:*
**Invalid param**

```json
{
    "status": "failed",
    "error": {
        "message": "Following parameters is invalid: Privacy field is invalid",
        "type": "ValidationErrorException",
        "code": 201,
        "support_message": "",
        "tracer": "4871a179e70e1ed20923b23c111863ab",
        "error_data": "#0 /var/www/html/475/PF.Site/Apps/p-advmarketplace-api/Service/MarketplaceApi.php(461): Apps\\Core_MobileApi\\Service\\AbstractApi->validationParamsError(Array)\n#1 /var/www/html/475/PF.Src/Core/Api/ApiServiceBase.php(204): Apps\\P_AdvMarketplaceAPI\\Service\\MarketplaceApi->update(Array)\n#2 /var/www/html/475/PF.Site/Apps/core-mobile-api/Service/AbstractApi.php(159): Core\\Api\\ApiServiceBase->process(Object(Core\\Route\\RouteUrl), Object(Apps\\phpFox_RESTful_API\\Service\\RestApiTransport), 'update')\n#3 /var/www/html/475/PF.Site/Apps/core-mobile-api/Service/ApiVersionResolver.php(186): Apps\\Core_MobileApi\\Service\\AbstractApi->process(Object(Core\\Route\\RouteUrl), Object(Apps\\phpFox_RESTful_API\\Service\\RestApiTransport), 'update')\n#4 /var/www/html/475/PF.Src/Core/Route/Controller.php(342): Apps\\Core_MobileApi\\Service\\ApiVersionResolver->process(Object(Core\\Route\\RouteUrl), Object(Apps\\phpFox_RESTful_API\\Service\\RestApiTransport), 'update')\n#5 /var/www/html/475/PF.Base/include/library/phpfox/module/module.class.php(355): Core\\Route\\Controller->get()\n#6 /var/www/html/475/PF.Base/include/library/phpfox/phpfox/phpfox.class.php(1594): Phpfox_Module->setController()\n#7 /var/www/html/475/PF.Base/start.php(631): Phpfox::run()\n#8 /var/www/html/475/index.php(5): require('/var/www/html/4...')\n#9 {main}",
        "validation_detail": {
            "privacy": "Privacy field is invalid"
        }
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
        "tracer": "6639c004dc2bc4d3c0447ae61f122670",
        "error_data": "#0 /var/www/html/475/PF.Site/Apps/core-mobile-api/Service/AbstractApi.php(319): Apps\\Core_MobileApi\\Service\\AbstractApi->permissionError('')\n#1 /var/www/html/475/PF.Site/Apps/p-advmarketplace-api/Service/MarketplaceApi.php(444): Apps\\Core_MobileApi\\Service\\AbstractApi->denyAccessUnlessGranted('edit', Object(Apps\\P_AdvMarketplaceAPI\\Api\\Resource\\MarketplaceResource))\n#2 /var/www/html/475/PF.Src/Core/Api/ApiServiceBase.php(204): Apps\\P_AdvMarketplaceAPI\\Service\\MarketplaceApi->update(Array)\n#3 /var/www/html/475/PF.Site/Apps/core-mobile-api/Service/AbstractApi.php(159): Core\\Api\\ApiServiceBase->process(Object(Core\\Route\\RouteUrl), Object(Apps\\phpFox_RESTful_API\\Service\\RestApiTransport), 'update')\n#4 /var/www/html/475/PF.Site/Apps/core-mobile-api/Service/ApiVersionResolver.php(186): Apps\\Core_MobileApi\\Service\\AbstractApi->process(Object(Core\\Route\\RouteUrl), Object(Apps\\phpFox_RESTful_API\\Service\\RestApiTransport), 'update')\n#5 /var/www/html/475/PF.Src/Core/Route/Controller.php(342): Apps\\Core_MobileApi\\Service\\ApiVersionResolver->process(Object(Core\\Route\\RouteUrl), Object(Apps\\phpFox_RESTful_API\\Service\\RestApiTransport), 'update')\n#6 /var/www/html/475/PF.Base/include/library/phpfox/module/module.class.php(355): Core\\Route\\Controller->get()\n#7 /var/www/html/475/PF.Base/include/library/phpfox/phpfox/phpfox.class.php(1594): Phpfox_Module->setController()\n#8 /var/www/html/475/PF.Base/start.php(631): Phpfox::run()\n#9 /var/www/html/475/index.php(5): require('/var/www/html/4...')\n#10 {main}"
    }
}
```
#### Detail Marketplace API
*Method:* 
`GET`

*Route:*
`advancedmarketplace/{{id}}`
    
*Response Success:*

```json
{
    "status": "success",
    "data": {
        "resource_name": "advancedmarketplace",
        "module_name": "advancedmarketplace",
        "title": "Edit Marketplace",
        "description": "Marketplace description",
        "short_description": "Marketplace short description",
        "text": "Marketplace description",
        "view_id": 0,
        "is_sponsor": false,
        "is_featured": true,
        "is_friend": false,
        "is_liked": false,
        "is_sell": 0,
        "is_closed": 0,
        "is_expired": null,
        "is_notified": false,
        "is_pending": false,
        "auto_sell": 0,
        "currency_id": "USD",
        "price": "Free",
        "location": null,
        "address": null,
        "payment_methods": [],
        "expired_date": "",
        "mark_sold": false,
        "is_draft": false,
        "image": {
            "50": "http://475.local/PF.Base/file/pic/advancedmarketplace/2019/04/1a540ef916c64a5a36e5ad94baea713a_120_square.png",
            "120": "http://475.local/PF.Base/file/pic/advancedmarketplace/2019/04/1a540ef916c64a5a36e5ad94baea713a_120_square.png",
            "200": "http://475.local/PF.Base/file/pic/advancedmarketplace/2019/04/1a540ef916c64a5a36e5ad94baea713a_200_square.png",
            "400": "http://475.local/PF.Base/file/pic/advancedmarketplace/2019/04/1a540ef916c64a5a36e5ad94baea713a_400_square.png",
            "image_url": "http://475.local/PF.Base/file/pic/advancedmarketplace/2019/04/1a540ef916c64a5a36e5ad94baea713a.png"
        },
        "images": [
            {
                "50": "http://475.local/PF.Base/file/pic/advancedmarketplace/2019/04/1a540ef916c64a5a36e5ad94baea713a_120_square.png",
                "120": "http://475.local/PF.Base/file/pic/advancedmarketplace/2019/04/1a540ef916c64a5a36e5ad94baea713a_120_square.png",
                "200": "http://475.local/PF.Base/file/pic/advancedmarketplace/2019/04/1a540ef916c64a5a36e5ad94baea713a_200_square.png",
                "400": "http://475.local/PF.Base/file/pic/advancedmarketplace/2019/04/1a540ef916c64a5a36e5ad94baea713a_400_square.png",
                "image_url": "http://475.local/PF.Base/file/pic/advancedmarketplace/2019/04/1a540ef916c64a5a36e5ad94baea713a.png"
            }
        ],
        "group_id": 0,
        "country": "United States",
        "province": "",
        "postal_code": null,
        "city": "Ho Chi Minh",
        "country_iso": "US",
        "country_child_id": 0,
        "statistic": {
            "total_like": 0,
            "total_comment": 1,
            "total_view": 1
        },
        "privacy": 0,
        "user": {
            "resource_name": "user",
            "module_name": "user",
            "full_name": "Admin",
            "avatar": "http://475.local/PF.Site/Apps/core-mobile-api/assets/images/default-images/user/no_image.png",
            "friend_id": 0,
            "id": 1
        },
        "categories": [
            {
                "resource_name": "advancedmarketplace_category",
                "name": "Community",
                "subs": null,
                "id": 1
            }
        ],
        "tags": [
            {
                "resource_name": "tag",
                "tag_text": "tag1",
                "id": 7
            },
            {
                "resource_name": "tag",
                "tag_text": "tag2",
                "id": 8
            }
        ],
        "buy_now_link": null,
        "time_stamp": "1555915870",
        "id": 4,
        "creation_date": "2019-04-22T06:51:10+00:00",
        "modification_date": null,
        "link": "http://475.local/index.php/advancedmarketplace/4/edit-marketplace/",
        "extra": {
            "can_view": true,
            "can_like": true,
            "can_share": true,
            "can_delete": true,
            "can_report": false,
            "can_add": true,
            "can_edit": false,
            "can_comment": true,
            "can_view_expired": true,
            "can_reopen": false,
            "can_feature": true,
            "can_approve": false,
            "can_sponsor": false,
            "can_sponsor_in_feed": false,
            "can_purchase_sponsor": false,
            "can_invite": false,
            "can_manage_photo": false,
            "can_buy_now": false
        },
        "feed_param": {
            "item_id": 4,
            "comment_type_id": "advancedmarketplace",
            "total_comment": 1,
            "like_type_id": "advancedmarketplace",
            "total_like": 0,
            "feed_title": "Edit Marketplace",
            "feed_link": "http://475.local/index.php/advancedmarketplace/4/edit-marketplace/",
            "feed_is_liked": false,
            "feed_is_friend": false,
            "report_module": "advancedmarketplace"
        }
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
        "tracer": "0d4cbb62e4bc0670ff63aeb4f9cbc469",
        "error_data": "#0 /var/www/html/475/PF.Site/Apps/p-advmarketplace-api/Service/MarketplaceApi.php(351): Apps\\Core_MobileApi\\Service\\AbstractApi->notFoundError()\n#1 /var/www/html/475/PF.Src/Core/Api/ApiServiceBase.php(204): Apps\\P_AdvMarketplaceAPI\\Service\\MarketplaceApi->findOne(Array)\n#2 /var/www/html/475/PF.Site/Apps/core-mobile-api/Service/AbstractApi.php(159): Core\\Api\\ApiServiceBase->process(Object(Core\\Route\\RouteUrl), Object(Apps\\phpFox_RESTful_API\\Service\\RestApiTransport), 'findOne')\n#3 /var/www/html/475/PF.Site/Apps/core-mobile-api/Service/ApiVersionResolver.php(186): Apps\\Core_MobileApi\\Service\\AbstractApi->process(Object(Core\\Route\\RouteUrl), Object(Apps\\phpFox_RESTful_API\\Service\\RestApiTransport), 'findOne')\n#4 /var/www/html/475/PF.Src/Core/Route/Controller.php(342): Apps\\Core_MobileApi\\Service\\ApiVersionResolver->process(Object(Core\\Route\\RouteUrl), Object(Apps\\phpFox_RESTful_API\\Service\\RestApiTransport), 'findOne')\n#5 /var/www/html/475/PF.Base/include/library/phpfox/module/module.class.php(355): Core\\Route\\Controller->get()\n#6 /var/www/html/475/PF.Base/include/library/phpfox/phpfox/phpfox.class.php(1594): Phpfox_Module->setController()\n#7 /var/www/html/475/PF.Base/start.php(631): Phpfox::run()\n#8 /var/www/html/475/index.php(5): require('/var/www/html/4...')\n#9 {main}"
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
        "tracer": "d6d1bedacab21f561dcf83cd6569872e",
        "error_data": "#0 /var/www/html/475/PF.Site/Apps/core-mobile-api/Service/AbstractApi.php(319): Apps\\Core_MobileApi\\Service\\AbstractApi->permissionError('')\n#1 /var/www/html/475/PF.Site/Apps/p-advmarketplace-api/Service/MarketplaceApi.php(305): Apps\\Core_MobileApi\\Service\\AbstractApi->denyAccessUnlessGranted('view', Object(Apps\\P_AdvMarketplaceAPI\\Api\\Resource\\MarketplaceResource))\n#2 /var/www/html/475/PF.Src/Core/Api/ApiServiceBase.php(204): Apps\\P_AdvMarketplaceAPI\\Service\\MarketplaceApi->findOne(Array)\n#3 /var/www/html/475/PF.Site/Apps/core-mobile-api/Service/AbstractApi.php(159): Core\\Api\\ApiServiceBase->process(Object(Core\\Route\\RouteUrl), Object(Apps\\phpFox_RESTful_API\\Service\\RestApiTransport), 'findOne')\n#4 /var/www/html/475/PF.Site/Apps/core-mobile-api/Service/ApiVersionResolver.php(186): Apps\\Core_MobileApi\\Service\\AbstractApi->process(Object(Core\\Route\\RouteUrl), Object(Apps\\phpFox_RESTful_API\\Service\\RestApiTransport), 'findOne')\n#5 /var/www/html/475/PF.Src/Core/Route/Controller.php(342): Apps\\Core_MobileApi\\Service\\ApiVersionResolver->process(Object(Core\\Route\\RouteUrl), Object(Apps\\phpFox_RESTful_API\\Service\\RestApiTransport), 'findOne')\n#6 /var/www/html/475/PF.Base/include/library/phpfox/module/module.class.php(355): Core\\Route\\Controller->get()\n#7 /var/www/html/475/PF.Base/include/library/phpfox/phpfox/phpfox.class.php(1594): Phpfox_Module->setController()\n#8 /var/www/html/475/PF.Base/start.php(631): Phpfox::run()\n#9 /var/www/html/475/index.php(5): require('/var/www/html/4...')\n#10 {main}"
    }
}
```

#### Delete Marketplace API
*Method:* 
`DELETE`

*Route:*
`advancedmarketplace/{{id}}`
    
*Response Success:*

```json
{
    "status": "success",
    "data": {},
    "message": "Successfully deleted listing.",
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
        "tracer": "c82c0d4167d6be84c48e80ae9e2a70ba",
        "error_data": "#0 /var/www/html/475/PF.Site/Apps/p-advmarketplace-api/Service/MarketplaceApi.php(482): Apps\\Core_MobileApi\\Service\\AbstractApi->notFoundError()\n#1 /var/www/html/475/PF.Src/Core/Api/ApiServiceBase.php(204): Apps\\P_AdvMarketplaceAPI\\Service\\MarketplaceApi->delete(Array)\n#2 /var/www/html/475/PF.Site/Apps/core-mobile-api/Service/AbstractApi.php(159): Core\\Api\\ApiServiceBase->process(Object(Core\\Route\\RouteUrl), Object(Apps\\phpFox_RESTful_API\\Service\\RestApiTransport), 'delete')\n#3 /var/www/html/475/PF.Site/Apps/core-mobile-api/Service/ApiVersionResolver.php(186): Apps\\Core_MobileApi\\Service\\AbstractApi->process(Object(Core\\Route\\RouteUrl), Object(Apps\\phpFox_RESTful_API\\Service\\RestApiTransport), 'delete')\n#4 /var/www/html/475/PF.Src/Core/Route/Controller.php(342): Apps\\Core_MobileApi\\Service\\ApiVersionResolver->process(Object(Core\\Route\\RouteUrl), Object(Apps\\phpFox_RESTful_API\\Service\\RestApiTransport), 'delete')\n#5 /var/www/html/475/PF.Base/include/library/phpfox/module/module.class.php(355): Core\\Route\\Controller->get()\n#6 /var/www/html/475/PF.Base/include/library/phpfox/phpfox/phpfox.class.php(1594): Phpfox_Module->setController()\n#7 /var/www/html/475/PF.Base/start.php(631): Phpfox::run()\n#8 /var/www/html/475/index.php(5): require('/var/www/html/4...')\n#9 {main}"
    }
}
```
**Permission denied**

```json
{
    "status": "failed",
    "error": {
        "message": "Item not found.",
        "type": "NotFoundErrorException",
        "code": 404,
        "support_message": "",
        "tracer": "c82c0d4167d6be84c48e80ae9e2a70ba",
        "error_data": "#0 /var/www/html/475/PF.Site/Apps/p-advmarketplace-api/Service/MarketplaceApi.php(482): Apps\\Core_MobileApi\\Service\\AbstractApi->notFoundError()\n#1 /var/www/html/475/PF.Src/Core/Api/ApiServiceBase.php(204): Apps\\P_AdvMarketplaceAPI\\Service\\MarketplaceApi->delete(Array)\n#2 /var/www/html/475/PF.Site/Apps/core-mobile-api/Service/AbstractApi.php(159): Core\\Api\\ApiServiceBase->process(Object(Core\\Route\\RouteUrl), Object(Apps\\phpFox_RESTful_API\\Service\\RestApiTransport), 'delete')\n#3 /var/www/html/475/PF.Site/Apps/core-mobile-api/Service/ApiVersionResolver.php(186): Apps\\Core_MobileApi\\Service\\AbstractApi->process(Object(Core\\Route\\RouteUrl), Object(Apps\\phpFox_RESTful_API\\Service\\RestApiTransport), 'delete')\n#4 /var/www/html/475/PF.Src/Core/Route/Controller.php(342): Apps\\Core_MobileApi\\Service\\ApiVersionResolver->process(Object(Core\\Route\\RouteUrl), Object(Apps\\phpFox_RESTful_API\\Service\\RestApiTransport), 'delete')\n#5 /var/www/html/475/PF.Base/include/library/phpfox/module/module.class.php(355): Core\\Route\\Controller->get()\n#6 /var/www/html/475/PF.Base/include/library/phpfox/phpfox/phpfox.class.php(1594): Phpfox_Module->setController()\n#7 /var/www/html/475/PF.Base/start.php(631): Phpfox::run()\n#8 /var/www/html/475/index.php(5): require('/var/www/html/4...')\n#9 {main}"
    }
}
```


#### Approve Marketplace API
*Method:* 
`PUT`

*Route:*
`advancedmarketplace/approve/{{id}}`
    
*Response Success:*

```json
{
    "status": "success",
    "data": {
        "can_view": true,
        "can_like": true,
        "can_share": true,
        "can_delete": false,
        "can_report": false,
        "can_add": true,
        "can_edit": true,
        "can_comment": true,
        "can_view_expired": true,
        "can_reopen": false,
        "can_feature": true,
        "can_approve": false,
        "can_sponsor": false,
        "can_sponsor_in_feed": false,
        "can_purchase_sponsor": false,
        "can_invite": true,
        "can_manage_photo": true,
        "can_buy_now": false,
        "is_pending": false
    },
    "message": "Listing has been approved.",
    "error": null
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
        "tracer": "67dfcbcbe46354dc09aa932c533a365c",
        "error_data": "#0 /var/www/html/475/PF.Site/Apps/core-mobile-api/Service/AbstractApi.php(319): Apps\\Core_MobileApi\\Service\\AbstractApi->permissionError('')\n#1 /var/www/html/475/PF.Site/Apps/p-advmarketplace-api/Service/MarketplaceApi.php(535): Apps\\Core_MobileApi\\Service\\AbstractApi->denyAccessUnlessGranted('approve', Object(Apps\\P_AdvMarketplaceAPI\\Api\\Resource\\MarketplaceResource))\n#2 /var/www/html/475/PF.Src/Core/Api/ApiServiceBase.php(204): Apps\\P_AdvMarketplaceAPI\\Service\\MarketplaceApi->approve(Array)\n#3 /var/www/html/475/PF.Site/Apps/core-mobile-api/Service/AbstractApi.php(159): Core\\Api\\ApiServiceBase->process(Object(Core\\Route\\RouteUrl), Object(Apps\\phpFox_RESTful_API\\Service\\RestApiTransport), 'approve')\n#4 /var/www/html/475/PF.Site/Apps/core-mobile-api/Service/ApiVersionResolver.php(186): Apps\\Core_MobileApi\\Service\\AbstractApi->process(Object(Core\\Route\\RouteUrl), Object(Apps\\phpFox_RESTful_API\\Service\\RestApiTransport), 'approve')\n#5 /var/www/html/475/PF.Src/Core/Route/Controller.php(342): Apps\\Core_MobileApi\\Service\\ApiVersionResolver->process(Object(Core\\Route\\RouteUrl), Object(Apps\\phpFox_RESTful_API\\Service\\RestApiTransport), 'approve')\n#6 /var/www/html/475/PF.Base/include/library/phpfox/module/module.class.php(355): Core\\Route\\Controller->get()\n#7 /var/www/html/475/PF.Base/include/library/phpfox/phpfox/phpfox.class.php(1594): Phpfox_Module->setController()\n#8 /var/www/html/475/PF.Base/start.php(631): Phpfox::run()\n#9 /var/www/html/475/index.php(5): require('/var/www/html/4...')\n#10 {main}"
    }
}
```

#### Feature/Unfeature Marketplace API
*Method:* 
`PUT`

*Request Type:*
`application/json`

*Route:*
`advancedmarketplace/feature/{{id}}`

*Request:*

```
feature = 1: Feature item
feature = 0: Unfeature item

{
	"feature" : 1
}
```

*Response Success:*

```json
{
    "status": "success",
    "data": {
        "is_featured": true
    },
    "message": "Listing successfully featured",
    "error": null
}
```
*Response Error:*
**Invalid param**

```json
{
    "status": "failed",
    "error": {
        "message": "Invalid API request parameters.",
        "type": "ValidationErrorException",
        "code": 202,
        "support_message": "",
        "tracer": "6ace5660de0605f6367641806d109e73",
        "error_data": "#0 /var/www/html/475/PF.Site/Apps/p-advmarketplace-api/Service/MarketplaceApi.php(546): Apps\\Core_MobileApi\\Service\\Helper\\ParametersResolver->resolveSingle(Array, 'feature', 'int', Array, 1)\n#1 /var/www/html/475/PF.Src/Core/Api/ApiServiceBase.php(204): Apps\\P_AdvMarketplaceAPI\\Service\\MarketplaceApi->feature(Array)\n#2 /var/www/html/475/PF.Site/Apps/core-mobile-api/Service/AbstractApi.php(159): Core\\Api\\ApiServiceBase->process(Object(Core\\Route\\RouteUrl), Object(Apps\\phpFox_RESTful_API\\Service\\RestApiTransport), 'feature')\n#3 /var/www/html/475/PF.Site/Apps/core-mobile-api/Service/ApiVersionResolver.php(186): Apps\\Core_MobileApi\\Service\\AbstractApi->process(Object(Core\\Route\\RouteUrl), Object(Apps\\phpFox_RESTful_API\\Service\\RestApiTransport), 'feature')\n#4 /var/www/html/475/PF.Src/Core/Route/Controller.php(342): Apps\\Core_MobileApi\\Service\\ApiVersionResolver->process(Object(Core\\Route\\RouteUrl), Object(Apps\\phpFox_RESTful_API\\Service\\RestApiTransport), 'feature')\n#5 /var/www/html/475/PF.Base/include/library/phpfox/module/module.class.php(355): Core\\Route\\Controller->get()\n#6 /var/www/html/475/PF.Base/include/library/phpfox/phpfox/phpfox.class.php(1594): Phpfox_Module->setController()\n#7 /var/www/html/475/PF.Base/start.php(631): Phpfox::run()\n#8 /var/www/html/475/index.php(5): require('/var/www/html/4...')\n#9 {main}"
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
        "tracer": "43ad548f2a7a906ce415085b3518f4b8",
        "error_data": "#0 /var/www/html/475/PF.Site/Apps/core-mobile-api/Service/AbstractApi.php(319): Apps\\Core_MobileApi\\Service\\AbstractApi->permissionError('')\n#1 /var/www/html/475/PF.Site/Apps/p-advmarketplace-api/Service/MarketplaceApi.php(548): Apps\\Core_MobileApi\\Service\\AbstractApi->denyAccessUnlessGranted('feature', Object(Apps\\P_AdvMarketplaceAPI\\Api\\Resource\\MarketplaceResource))\n#2 /var/www/html/475/PF.Src/Core/Api/ApiServiceBase.php(204): Apps\\P_AdvMarketplaceAPI\\Service\\MarketplaceApi->feature(Array)\n#3 /var/www/html/475/PF.Site/Apps/core-mobile-api/Service/AbstractApi.php(159): Core\\Api\\ApiServiceBase->process(Object(Core\\Route\\RouteUrl), Object(Apps\\phpFox_RESTful_API\\Service\\RestApiTransport), 'feature')\n#4 /var/www/html/475/PF.Site/Apps/core-mobile-api/Service/ApiVersionResolver.php(186): Apps\\Core_MobileApi\\Service\\AbstractApi->process(Object(Core\\Route\\RouteUrl), Object(Apps\\phpFox_RESTful_API\\Service\\RestApiTransport), 'feature')\n#5 /var/www/html/475/PF.Src/Core/Route/Controller.php(342): Apps\\Core_MobileApi\\Service\\ApiVersionResolver->process(Object(Core\\Route\\RouteUrl), Object(Apps\\phpFox_RESTful_API\\Service\\RestApiTransport), 'feature')\n#6 /var/www/html/475/PF.Base/include/library/phpfox/module/module.class.php(355): Core\\Route\\Controller->get()\n#7 /var/www/html/475/PF.Base/include/library/phpfox/phpfox/phpfox.class.php(1594): Phpfox_Module->setController()\n#8 /var/www/html/475/PF.Base/start.php(631): Phpfox::run()\n#9 /var/www/html/475/index.php(5): require('/var/www/html/4...')\n#10 {main}"
    }
}
```

#### Sponsor/Unsponsor Marketplace API
*Method:* 
`PUT`

*Request Type:*
`application/json`

*Route:*
`advancedmarketplace/sponsor/{{id}}`

*Request:*

```
sponsor = 1: Sponsor item
sponsor = 0: Unsponsor item

{
	"sponsor" : 1
}
```

*Response Success:*

```json
{
    "status": "success",
    "data": {
        "is_sponsor": true
    },
    "message": "Listing successfully sponsored",
    "error": null
}
```
*Response Error:*
**Invalid param**

```json
{
    "status": "failed",
    "error": {
        "message": "Invalid API request parameters.",
        "type": "ValidationErrorException",
        "code": 202,
        "support_message": "",
        "tracer": "2aa760fda3cfd5940754f2514abfbebc",
        "error_data": "#0 /var/www/html/475/PF.Site/Apps/p-advmarketplace-api/Service/MarketplaceApi.php(562): Apps\\Core_MobileApi\\Service\\Helper\\ParametersResolver->resolveSingle(Array, 'sponsor', 'int', Array, 1)\n#1 /var/www/html/475/PF.Src/Core/Api/ApiServiceBase.php(204): Apps\\P_AdvMarketplaceAPI\\Service\\MarketplaceApi->sponsor(Array)\n#2 /var/www/html/475/PF.Site/Apps/core-mobile-api/Service/AbstractApi.php(159): Core\\Api\\ApiServiceBase->process(Object(Core\\Route\\RouteUrl), Object(Apps\\phpFox_RESTful_API\\Service\\RestApiTransport), 'sponsor')\n#3 /var/www/html/475/PF.Site/Apps/core-mobile-api/Service/ApiVersionResolver.php(186): Apps\\Core_MobileApi\\Service\\AbstractApi->process(Object(Core\\Route\\RouteUrl), Object(Apps\\phpFox_RESTful_API\\Service\\RestApiTransport), 'sponsor')\n#4 /var/www/html/475/PF.Src/Core/Route/Controller.php(342): Apps\\Core_MobileApi\\Service\\ApiVersionResolver->process(Object(Core\\Route\\RouteUrl), Object(Apps\\phpFox_RESTful_API\\Service\\RestApiTransport), 'sponsor')\n#5 /var/www/html/475/PF.Base/include/library/phpfox/module/module.class.php(355): Core\\Route\\Controller->get()\n#6 /var/www/html/475/PF.Base/include/library/phpfox/phpfox/phpfox.class.php(1594): Phpfox_Module->setController()\n#7 /var/www/html/475/PF.Base/start.php(631): Phpfox::run()\n#8 /var/www/html/475/index.php(5): require('/var/www/html/4...')\n#9 {main}"
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
        "tracer": "6ad5a99ce6660b77eb4ef9dc247459a7",
        "error_data": "#0 /var/www/html/475/PF.Site/Apps/core-mobile-api/Service/AbstractApi.php(319): Apps\\Core_MobileApi\\Service\\AbstractApi->permissionError('')\n#1 /var/www/html/475/PF.Site/Apps/p-advmarketplace-api/Service/MarketplaceApi.php(564): Apps\\Core_MobileApi\\Service\\AbstractApi->denyAccessUnlessGranted('sponsor', Object(Apps\\P_AdvMarketplaceAPI\\Api\\Resource\\MarketplaceResource))\n#2 /var/www/html/475/PF.Src/Core/Api/ApiServiceBase.php(204): Apps\\P_AdvMarketplaceAPI\\Service\\MarketplaceApi->sponsor(Array)\n#3 /var/www/html/475/PF.Site/Apps/core-mobile-api/Service/AbstractApi.php(159): Core\\Api\\ApiServiceBase->process(Object(Core\\Route\\RouteUrl), Object(Apps\\phpFox_RESTful_API\\Service\\RestApiTransport), 'sponsor')\n#4 /var/www/html/475/PF.Site/Apps/core-mobile-api/Service/ApiVersionResolver.php(186): Apps\\Core_MobileApi\\Service\\AbstractApi->process(Object(Core\\Route\\RouteUrl), Object(Apps\\phpFox_RESTful_API\\Service\\RestApiTransport), 'sponsor')\n#5 /var/www/html/475/PF.Src/Core/Route/Controller.php(342): Apps\\Core_MobileApi\\Service\\ApiVersionResolver->process(Object(Core\\Route\\RouteUrl), Object(Apps\\phpFox_RESTful_API\\Service\\RestApiTransport), 'sponsor')\n#6 /var/www/html/475/PF.Base/include/library/phpfox/module/module.class.php(355): Core\\Route\\Controller->get()\n#7 /var/www/html/475/PF.Base/include/library/phpfox/phpfox/phpfox.class.php(1594): Phpfox_Module->setController()\n#8 /var/www/html/475/PF.Base/start.php(631): Phpfox::run()\n#9 /var/www/html/475/index.php(5): require('/var/www/html/4...')\n#10 {main}"
    }
}
```

#### Report Marketplace API
*Method:* 
`POST`

*Request Type:*
`application/json`

*Route:*
`report`

*Request:*

```
{
    "reason" : 1,
    "item_id" : 7,
    "item_type" : "advancedmarketplace",
    "feedback" : "Report advmarketplace"
}
```
    
*Response Success:*

```json
{
    "status": "success",
    "data": {
        "report": true
    },
    "message": "",
    "error": null
}
```

#### Wishlist Marketplace API
    
*Method:* 
`PUT`

*Request Type:*
`application/json`

*Route:*
`advancedmarketplace/wishlist/{{id}}`

*Request:*
```
is_wishlist = false: add to wishlist
is_wishlist = true: remove from wishlist

{
	"is_wishlist" : false
}
```

*Response Success:*

```json
{
    "status": "success",
    "data": {
        "is_wishlist": true
    },
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
        "message": "Invalid API request parameters.",
        "type": "ValidationErrorException",
        "code": 202,
        "support_message": "",
        "tracer": "0fbe90f95c824788c7daeb68357023e1",
        "error_data": "#0 /var/www/html/475/PF.Site/Apps/p-advmarketplace-api/Service/MarketplaceApi.php(629): Apps\\Core_MobileApi\\Service\\Helper\\ParametersResolver->resolveSingle(Array, 'is_wishlist', 'bool', Array, false)\n#1 /var/www/html/475/PF.Src/Core/Api/ApiServiceBase.php(204): Apps\\P_AdvMarketplaceAPI\\Service\\MarketplaceApi->wishlist(Array)\n#2 /var/www/html/475/PF.Site/Apps/core-mobile-api/Service/AbstractApi.php(159): Core\\Api\\ApiServiceBase->process(Object(Core\\Route\\RouteUrl), Object(Apps\\phpFox_RESTful_API\\Service\\RestApiTransport), 'wishlist')\n#3 /var/www/html/475/PF.Site/Apps/core-mobile-api/Service/ApiVersionResolver.php(186): Apps\\Core_MobileApi\\Service\\AbstractApi->process(Object(Core\\Route\\RouteUrl), Object(Apps\\phpFox_RESTful_API\\Service\\RestApiTransport), 'wishlist')\n#4 /var/www/html/475/PF.Src/Core/Route/Controller.php(342): Apps\\Core_MobileApi\\Service\\ApiVersionResolver->process(Object(Core\\Route\\RouteUrl), Object(Apps\\phpFox_RESTful_API\\Service\\RestApiTransport), 'wishlist')\n#5 /var/www/html/475/PF.Base/include/library/phpfox/module/module.class.php(355): Core\\Route\\Controller->get()\n#6 /var/www/html/475/PF.Base/include/library/phpfox/phpfox/phpfox.class.php(1594): Phpfox_Module->setController()\n#7 /var/www/html/475/PF.Base/start.php(631): Phpfox::run()\n#8 /var/www/html/475/index.php(5): require('/var/www/html/4...')\n#9 {main}"
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
        "tracer": "6ad5a99ce6660b77eb4ef9dc247459a7",
        "error_data": "#0 /var/www/html/475/PF.Site/Apps/core-mobile-api/Service/AbstractApi.php(319): Apps\\Core_MobileApi\\Service\\AbstractApi->permissionError('')\n#1 /var/www/html/475/PF.Site/Apps/p-advmarketplace-api/Service/MarketplaceApi.php(564): Apps\\Core_MobileApi\\Service\\AbstractApi->denyAccessUnlessGranted('sponsor', Object(Apps\\P_AdvMarketplaceAPI\\Api\\Resource\\MarketplaceResource))\n#2 /var/www/html/475/PF.Src/Core/Api/ApiServiceBase.php(204): Apps\\P_AdvMarketplaceAPI\\Service\\MarketplaceApi->sponsor(Array)\n#3 /var/www/html/475/PF.Site/Apps/core-mobile-api/Service/AbstractApi.php(159): Core\\Api\\ApiServiceBase->process(Object(Core\\Route\\RouteUrl), Object(Apps\\phpFox_RESTful_API\\Service\\RestApiTransport), 'sponsor')\n#4 /var/www/html/475/PF.Site/Apps/core-mobile-api/Service/ApiVersionResolver.php(186): Apps\\Core_MobileApi\\Service\\AbstractApi->process(Object(Core\\Route\\RouteUrl), Object(Apps\\phpFox_RESTful_API\\Service\\RestApiTransport), 'sponsor')\n#5 /var/www/html/475/PF.Src/Core/Route/Controller.php(342): Apps\\Core_MobileApi\\Service\\ApiVersionResolver->process(Object(Core\\Route\\RouteUrl), Object(Apps\\phpFox_RESTful_API\\Service\\RestApiTransport), 'sponsor')\n#6 /var/www/html/475/PF.Base/include/library/phpfox/module/module.class.php(355): Core\\Route\\Controller->get()\n#7 /var/www/html/475/PF.Base/include/library/phpfox/phpfox/phpfox.class.php(1594): Phpfox_Module->setController()\n#8 /var/www/html/475/PF.Base/start.php(631): Phpfox::run()\n#9 /var/www/html/475/index.php(5): require('/var/www/html/4...')\n#10 {main}"
    }
}
```