<?php 

namespace BetterPHP\Abstracts;

abstract class AbstractPostType
{
	public function __construct(private string $singular, private string $plural, private string $textdomain)
	{
		add_action('init', [$this, 'register']);
        add_filter('post_updated_messages', [$this, 'messages']);
	}

	public function register()
	{
		$supports = apply_filters(
			$this->getSlug() . '_post_type_supports',
			['title', 'editor', 'thumbnail', 'page-attributes']
		);

		$labels = [
            'name' => __($this->plural, $this->textdomain),
            'singular_name' => __($this->singular, $this->textdomain),
            'add_new' => __('Add New', $this->textdomain),
            'add_new_item' => __('Add New ' . $this->singular, $this->textdomain),
            'edit_item' => __('Edit ' . $this->singular, $this->textdomain),
            'new_item' => __('New ' . $this->singular, $this->textdomain),
            'all_items' => __('All ' . $this->plural, $this->textdomain),
            'view_item' => __('View ' . $this->plural, $this->textdomain),
            'search_items' => __('Search ' . $this->plural, $this->textdomain),
            'not_found' => __('No ' . strtolower($this->plural) . ' found', $this->textdomain),
            'not_found_in_trash' => __('No ' . strtolower($this->plural) . ' found in Trash', $this->textdomain),
            'parent_item_colon' =>  __('Select Parent Page', $this->textdomain),
            'menu_name' => __($this->plural, $this->textdomain)
        ];

        $args = [
            'labels' => $labels,
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'show_in_nav_menus'	=> true,
            'query_var' => true,
            'rewrite' => ['slug' => $this->getSlug(), 'with_front' => true],
            'capability_type' => 'page',
            'has_archive' => false,
            'hierarchical' => true,
            'menu_position' => null,
            'supports' => $supports,
            'yarpp_support' => true,
            'taxonomies' => ['category'],
            'show_in_rest' => true,
            'rest_base' => $this->getSlug(),
            'rest_controller_class' => 'WP_REST_Posts_Controller'
        ];

        register_post_type(
        	$this->getSlug(),
        	apply_filters($this->getSlug() . '_post_type_config', $args)
        );
	}

	public function messages()
	{
		global $post, $post_ID;
  
  		$messages[$this->singular] = [
  			0 => '', 
		    1 => sprintf(__($this->singular . ' updated. <a href="%s">View </a>' . $this->singular), esc_url(get_permalink($post_ID))),
		    2 => __('Custom field updated.'),
		    3 => __('Custom field deleted.'),
		    4 => __($this->singular . ' updated.'),
		    5 => isset($_GET['revision']) ? sprintf( __($this->singular . ' restored to revision from %s'), wp_post_revision_title((int) $_GET['revision'], false)) : false,
		    6 => sprintf(__($this->singular .' published. <a href="%s">View ' . strtolower($this->singular) . '</a>'), esc_url(get_permalink($post_ID))),
		    7 => __($this->singular . ' saved.'),
		    8 => sprintf(__($this->singular . ' submitted. <a target="_blank" href="%s">Preview ' . strtolower($this->singular) . '</a>'), esc_url(add_query_arg( 'preview', 'true', get_permalink($post_ID)))),
		    9 => sprintf(__($this->singular . ' scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview ' . strtolower($this->singular) . '</a>'), date_i18n( __('M j, Y @ G:i'), strtotime($post->post_date)), esc_url( get_permalink($post_ID))),
		    10 => sprintf(__($this->singular . ' draft updated. <a target="_blank" href="%s">Preview ' . strtolower($this->singular) . '</a>'), esc_url(add_query_arg('preview', 'true', get_permalink($post_ID)))),
  		];

  		return $messages;
	}

	private function getSlug()
	{
		return strtolower(trim(
			preg_replace('/[^A-Za-z0-9-]+/', '-', $this->singular)
		));
	}

	public function setMenu(string $menu)
	{
		$this->menu = $menu;
	}
}