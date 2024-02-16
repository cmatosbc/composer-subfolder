<?php 

namespace BetterPHP\Abstracts;

abstract class AbstractTaxonomy
{
	public function __construct(private string $singular, private string $plural, private array $postTypes, private string $textdomain)
	{
		$this->register();
	}

	public function register()
	{
		$labels = [
			'name' => _x($this->plural, $this->textdomain),
			'singular_name' => _x($this->singular, $this->textdomain),
			'search_items' => __('Search ' . $this->plural, $this->textdomain),
			'all_items' => __('All ' . $this->plural, $this->textdomain),
			'parent_item' => __('Parent ' . $this->singular, $this->textdomain),
			'parent_item_colon' => __('Parent ' . $this->singular . ':', $this->textdomain),
			'edit_item' => __('Edit ' . $this->singular, $this->textdomain),
			'update_item' => __('Update ' . $this->singular, $this->textdomain),
			'add_new_item' => __('Add New ' . $this->singular, $this->textdomain),
			'new_item_name' => __('New ' . $this->singular . ' Name', $this->textdomain),
			'menu_name' => __($this->singular, $this->textdomain),
		];

		$args = [
			'labels' => $labels,
			'public' => true,
			'show_in_nav_menus' => true,
			'show_admin_column' => false,
			'hierarchical' => false,
			'show_tagcloud' => true,
			'show_ui' => true,
			'show_in_rest' => true,
			'query_var' => true,
			'rewrite' => true,
			'capabilities' => [],
		];

		register_taxonomy(
			$this->getSlug(),
			apply_filters($this->getSlug() . '_taxonomy_post_types_to_include', $this->postTypes),
			apply_filters($this->getSlug() . '_taxonomy_args', $args)
		);
	}

	private function getSlug()
	{
		return strtolower(trim(
			preg_replace('/[^A-Za-z0-9-]+/', '-', $this->singular)
		));
	}
}