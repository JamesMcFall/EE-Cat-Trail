Cat-Trail
============

An ExpressionEngine plugin making it easy to build URLs and breadcrumbs for categories and channel entries on sites that have multiple levels of categories.

## Installation
Unzip or clone the plugin directory down. From there installation is as per any other EE plugin. Copy the plugin dir (cat_trail) into __system/expressionengine/third_party/__.

## Usage
### Getting the URL for a category
Getting the URL for a category is very simple using Cat_trail. In the below example you can see I'm building the href value for a given category. You need to supply the __cat_id__ parameter.

```html
<a href="/categories/{exp:cat_trail:get_cat_url cat_id='{cat_id}'}">{cat_name}</a>
```
Would result in:
```html
<a href="/categories/category-one/category-two/my-category">My Category</a>
```

### Getting the URL for a channel entry
_@todo_

### More To Come
I'm intending to build the functionality to use a tag pair to return full categories for a supplied category or channel entry. The main use of this for myself would be building breadcrumbs on large product/category websites.
