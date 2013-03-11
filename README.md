Cat-Trail V0.2
=============

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

But note, if you're using another plugin like child_categories, ExpressionEngine parses nested plugins from inward outwards, so you need to specify the outer plugin to parse first (with parse="inward") so that the category ID is available and processed for the plugin to use.

```html
<!-- Output a link to each child category -->
{exp:child_categories parent="{last_segment_category_id}" show_empty="yes" parse="inward"} <!-- See? -->
    {child_category_start}
        <a href="{exp:cat_trail:get_cat_url cat_id='{child_category_id}'}" class="browseAll">
            {child_category_name}.
        </a>
    {child_category_end}
{/exp:child_categories}
```

### Looping through the parents of the supplied category
Once you are a number of categories in, producing breadcrumbs can be a bit of a pain. Using the __get_cat_structure__ tag pair, you can get the category parents for the supplied category and output nice easy breadcrumbs.
```html
<!-- Output breadcrumbs for the current category -->
<ul class="breadCrumb">
    {exp:cat_trail:get_cat_structure cat_id="{last_segment_category_id}" parse="inward"} <!-- Need this so it runs first -->
        <!-- To build the proper full category URL we can call the get_cat_url method again -->
        <li {if {last_segment_category_id} == {category_id}}class="current"{/if}>
            <a href="/products/{exp:cat_trail:get_cat_url cat_id='{category_id}'}">{category_name}</a>
        </li>
    {/exp:cat_trail:get_cat_structure}
</ul>
```

### More To Come!!!
