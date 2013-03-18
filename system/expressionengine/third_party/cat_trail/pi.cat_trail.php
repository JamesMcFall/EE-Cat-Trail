<?php

$plugin_info = array(
    'pi_name'           => 'Cat Trail',
    'pi_version'        => '0.3',
    'pi_author'         => 'James McFall',
    'pi_author_url'     => 'http://mcfall.geek.nz/',
    'pi_description'    => 'Maintaining nice category URLs in EE can be a pain
                            when there are more than one level of category. This
                            plugin makes it easy to assemble urls and loop 
                            through the parent categories for a supplied 
                            category ID or category URL title.',
    'pi_usage'          => null
);

class Cat_trail {

    /**
     * Constructor
     */
    function __construct() {
        $this->EE = & get_instance();
    }

    /**
     * Return the url string for this category or channel entry.
     * 
     * Pass in a category id or category url title and get back a proper 
     * hierarchical url string e.g. for a product you'd get a url like:
     * products/cat_1/cat_2/cat_3/product_url_title (excluding the "products"
     * template group at the front).
     * 
     * @return <string> $url_string
     */
    public function get_cat_url() {
       
        # The cat_id or cat_url parameter can be used here.
        $cat_id  = $this->EE->TMPL->fetch_param('cat_id');
        $cat_url = $this->EE->TMPL->fetch_param('cat_url');
                
        # If cat_id isn't supplied (but cat_url is), look up the id using that.
        if (!$cat_id && $cat_url) {
            $cat_id = $this->_get_category_id_from_url_title($cat_url);
        }
        
        # Get the ordered category trail
        $category_structure = array_reverse($this->_assemble_category_structure($cat_id));
        
        # Append each category to the return string
        $href = "";
        foreach ($category_structure as $category) {
            $href .= $category->cat_url_title . "/";
        }
        
        # Strip the last slash off
        return rtrim($href, "/");
    }

    /**
     * Provide an EE template loop for all of the categories under the supplied 
     * category.
     * 
     * @return <string> The repeated markup blocks between the plugin tags with
     *                  the EE category template vars replaced.
     */
    public function get_cat_structure() {
        
        # Save everything between the two tags so we can output it for each category
        $markup_template = $this->EE->TMPL->tagdata;
        $output_markup = "";
        $count = 0;
        
        # The cat_id or cat_url parameter can be used here.
        $cat_id  = $this->EE->TMPL->fetch_param('cat_id');
        $cat_url = $this->EE->TMPL->fetch_param('cat_url');
                
        # If cat_id isn't supplied (but cat_url is), look up the id using that.
        if (!$cat_id && $cat_url) {
            $cat_id = $this->_get_category_id_from_url_title($cat_url);
        }
        
        # Get the ordered category trail
        $category_structure = array_reverse($this->_assemble_category_structure($cat_id));
                
        # Foreach category, take a copy of the markup template and replace all of
        # the template tags. Then append to the output string.
        foreach ($category_structure as $category) {
            
            $count ++;
            
            # Replace all the same tags used in the EE categories tag
            $tmp_output = $markup_template;
            $tmp_output = str_replace('{category_id}',          $category->cat_id, $tmp_output);
            $tmp_output = str_replace('{category_description}', $category->cat_description, $tmp_output);
            $tmp_output = str_replace('{parent_id}',            $category->parent_id, $tmp_output);
            $tmp_output = str_replace('{category_image}',       $category->cat_image, $tmp_output);
            $tmp_output = str_replace('{category_name}',        $category->cat_name, $tmp_output);
            $tmp_output = str_replace('{category_url_title}',   $category->cat_url_title, $tmp_output);
            $tmp_output = str_replace('{count}',                $count."", $tmp_output);
            $tmp_output = str_replace('{total_results}',        count($category_structure), $tmp_output);
            
            # Append this to the output markup
            $output_markup .= $tmp_output;
        }
        
        return $output_markup;
    }
    
    /**
     * Assemble the category structure from bottom to top for this category ID.
     * 
     * @param <int> $cat_id
     * @param <array> $categories - passed by reference so we can recursively 
     *                call this method
     * @return <array>
     */
    private function _assemble_category_structure($cat_id, &$categories = array()) {

        # Get this category from the DB
        $this->EE->db->select("*")
                ->from("exp_categories")
                ->where("cat_id", $cat_id)
                ->limit(1);

        $result = $this->EE->db->get();

        # If a category is returned add it to the array
        if ($result->num_rows()) {
            $category = $result->row();
            $categories[] = $category;

            # Does this category still have a parent? If so we want that too.
            if ($category->parent_id) {
                # Headachey recursion!
                $this->_assemble_category_structure($category->parent_id, $categories);
            }
        }

        return $categories;
    }
    
    /**
     * Get the category id using the URL title.
     * 
     * @param <string> $url_title
     * @return <int|boolean> $cat_id (either the id or false).
     */
    protected function _get_category_id_from_url_title($url_title) {
        # Get this category from the DB
        $this->EE->db->select("*")
                ->from("exp_categories")
                ->where("cat_url_title", $url_title)
                ->limit(1);

        $result = $this->EE->db->get();
        
        if ($result->num_rows()) {
            $row = $result->row();
            return $row->cat_id;
        }
            
        return false;
    }
}

?>
