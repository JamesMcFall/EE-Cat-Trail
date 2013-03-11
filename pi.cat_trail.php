<?php

$plugin_info = array(
    'pi_name'           => 'Cat Trail',
    'pi_version'        => '0.1',
    'pi_author'         => 'James McFall',
    'pi_author_url'     => 'http://mcfall.geek.nz/',
    'pi_description'    => 'Maintaining nice category URLs in EE can be a pain
                            when there are more than one level of category. This
                            plugin makes it easy to assemble urls for categories 
                            and channel entries that respect the category 
                            heirarchy.',
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
     * Pass in a channel entry id or category id and get back a proper 
     * hierarchical url string e.g. for a product you'd get a url like:
     * products/cat_1/cat_2/cat_3/product_url_title (excluding the "products"
     * template group at the front).
     * 
     * @return <string> $url_string
     */
    public function get_cat_url() {
       
        # Either a category ID or an entry ID have to be supplied
        $cat_id = $this->EE->TMPL->fetch_param('cat_id');

        $category_structure = array_reverse($this->_assemble_category_structure($cat_id));
        
        # Append each category to the return string
        $href = "";
        foreach ($category_structure as $category) {
            $href .= "/" . $category->cat_url_title;
        }

        return $href;
    }

    public function get_entry_url() {
        throw new Exception("get_entry_url not yet implemented.");
    }
    
    /**
     * Assemble the category structure from bottom to top for this category ID
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
}

?>

