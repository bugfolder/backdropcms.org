<?php
/**
 * @file
 * Basis preprocess functions and theme function overrides.
 */

/**
 * Implements hook_css_alter().
 */
function basis_css_alter(&$css) {
  // Remove the Basis css/component/menu-dropdown.css and add breakpoint files
  // if using a custom breakpoint.
  $config = config('menu.settings');
  $path = backdrop_get_path('theme', 'basis');
  if (isset($css[$path . '/css/component/menu-dropdown.css']) && $config->get('menu_breakpoint') == 'custom') {
    $dropdown_css = $css[$path . '/css/component/menu-dropdown.css'];
    unset($css[$path . '/css/component/menu-dropdown.css']);

    $weight = $dropdown_css['weight'];
    $weight += 0.0001;
    $css[$path . '/css/component/menu-dropdown.breakpoint.css'] = $dropdown_css;
    $css[$path . '/css/component/menu-dropdown.breakpoint.css']['weight'] = $weight;
    $css[$path . '/css/component/menu-dropdown.breakpoint.css']['data'] = $path . '/css/component/menu-dropdown.breakpoint.css';

    $weight += 0.0001;
    $css[$path . '/css/component/menu-dropdown.breakpoint-queries.css'] = $dropdown_css;
    $css[$path . '/css/component/menu-dropdown.breakpoint-queries.css']['weight'] = $weight;
    $css[$path . '/css/component/menu-dropdown.breakpoint-queries.css']['media'] = 'all and (min-width: ' . $config->get('menu_breakpoint_custom') . ')';
    $css[$path . '/css/component/menu-dropdown.breakpoint-queries.css']['data'] = $path . '/css/component/menu-dropdown.breakpoint-queries.css';
  }
}

/**
 * Prepares variables for page templates.
 *
 * @see page.tpl.php
 */
function basis_preprocess_page(&$variables) {
  $node = menu_get_object();

  // Add the OpenSans font from core on every page of the site.
  backdrop_add_library('system', 'opensans', TRUE);

  // To add a class 'page-node-[nid]' to each page.
  if ($node) {
    $variables['classes'][] = 'page-node-' . $node->nid;
  }

  // To add a class 'view-name-[name]' to each page.
  $view = views_get_page_view();
  if ($view) {
    $variables['classes'][] = 'view-name-' . $view->name;
  }
}

/**
 * Prepares variables for maintenance page templates.
 *
 * @see maintenance-page.tpl.php
 */
function basis_preprocess_maintenance_page(&$variables) {
  $css_path = backdrop_get_path('theme', 'basis') . '/css/component/maintenance.css';
  backdrop_add_css($css_path);
}

/**
 * Prepares variables for layout templates.
 *
 * @see layout.tpl.php
 */
function basis_preprocess_layout(&$variables) {
  if ($variables['is_front']) {
    // Add a special front-page class.
    $variables['classes'][] = 'layout-front';
    // Add a special front-page template suggestion.
    $original = $variables['theme_hook_original'];
    $variables['theme_hook_suggestions'][] = $original . '__front';
    $variables['theme_hook_suggestion'] = $original . '__front';
  }
}

/**
 * Prepares variables for node templates.
 *
 * @see node.tpl.php
 */
function basis_preprocess_node(&$variables) {
  if ($variables['status'] == NODE_NOT_PUBLISHED) {
    $name = node_type_get_name($variables['type']);
    $variables['title_suffix']['unpublished_indicator'] = array(
      '#type' => 'markup',
      '#markup' => '<div class="unpublished-indicator">' . t('This @type is unpublished.', array('@type' => $name)) . '</div>',
    );
  }
}

/**
 * Prepares variables for header templates.
 *
 * @see header.tpl.php
 */
function basis_preprocess_header(&$variables) {
  $logo = $variables['logo'];
  $logo_attributes = $variables['logo_attributes'];

  // Add classes and height/width to logo.
  if ($logo) {
    $logo_wrapper_classes = array();
    $logo_wrapper_classes[] = 'header-logo-wrapper';
    if ($logo_attributes['width'] <= $logo_attributes['height']) {
      $logo_wrapper_classes[] = 'header-logo-tall';
    }

    $variables['logo_wrapper_classes'] = $logo_wrapper_classes;
  }
}

/**
 * Overrides theme_breadcrumb(). Removes &raquo; from markup.
 *
 * @see theme_breadcrumb().
 */
function basis_breadcrumb($variables) {
  $breadcrumb = $variables['breadcrumb'];
  $output = '';
  if (!empty($breadcrumb)) {
    $output .= '<nav class="breadcrumb" aria-label="' . t('Website Orientation') . '">';
    $output .= '<ol><li>' . implode('</li><li>', $breadcrumb) . '</li></ol>';
    $output .= '</nav>';
  }
  return $output;
}
