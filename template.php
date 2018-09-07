<?php
/**
 * Override of theme('breadcrumb').
 */
function dgr_rubik_breadcrumb($vars) {
  $output = '';

  if (isset($vars['breadcrumb'][3])) {  // If we are 4 levels deep in the breadcrumb
    $pages = array("News", "Articles", "Press Releases");
    preg_match('/(?<=\>).*?(?=\<)/', $vars['breadcrumb'][1], $lists);
  }

  // Optional: Add the site name to the front of the stack.
  if (!empty($vars['prepend'])) {
    $site_name = empty($vars['breadcrumb']) ? "<strong>". check_plain(variable_get('site_name', '')) ."</strong>" : l(variable_get('site_name', ''), '<front>', array('purl' => array('disabled' => TRUE)));
    array_unshift($vars['breadcrumb'], $site_name);
  }

  $exceptions = array("News", "Noticias", "Articles", "Artículos", "Press Releases", "Comunicados de Prensa", "Administration", "Administración", "Categories", "Categorías");
  preg_match('/(?<=\>).*?(?=\<)/', $vars['breadcrumb'][1], $list);
  if (in_array($list[0], $exceptions)) {
    $exempted = TRUE;
  }

  $depth = 0;
  $separator = '  » ';
  foreach ($vars['breadcrumb'] as $link) {

    // If the item isn't a link, surround it with a strong tag to format it like
    // one.
    if (!preg_match('/^<a/', $link) && !preg_match('/^<strong/', $link)) {
      $link = '<strong>' . $link . '</strong>';
    }

    $output .= "<span class='breadcrumb-link breadcrumb-depth-{$depth}'>{$link}</span>";
    $depth++;

    if ($link !== end($vars['breadcrumb'])) {   // Add separators, unless we're on the last item
      $output .= "<span class='bc-separator'>{$separator}</span>" ;
    }
  }

  if (($depth > 2) || ($exempted)) { // Only show breadcrumbs if we have more than 2 links or if we are on the exempted pages
    return $output;
  }
}

/**
 * Override of date_all_day_label.
 */
function dgr_rubik_date_all_day_label() {
  return '';
}

function dgr_rubik_link_field_process($element, $form_state, $complete_form) {
  variable_set('title_description', $element['title']['#description']);
  $instance = field_widget_instance($element, $form_state);
  $settings = $instance['settings'];
  $element['url'] = array(
    '#type' => 'textfield',
    '#maxlength' => LINK_URL_MAX_LENGTH,
    '#title' => t('URL'),
    '#required' => ($element['#delta'] == 0 && $settings['url'] !== 'optional') ? $element['#required'] : FALSE,
    '#default_value' => isset($element['#value']['url']) ? $element['#value']['url'] : NULL,
  );
  if ($settings['title'] !== 'none' && $settings['title'] !== 'value') {
    // Figure out the label of the title field.
    if (!empty($settings['title_label_use_field_label'])) {
      // Use the element label as the title field label.
      $title_label = $element['#title'];
      // Hide the field label because there is no need for the duplicate labels.
      $element['#title_display'] = 'invisible';
    }
    else {
      $title_label = t('Title');
    }

    $element['title'] = array(
      '#type' => 'textfield',
      '#maxlength' => $settings['title_maxlength'],
      '#title' => $title_label,
      '#description' => t(''),
      '#required' => ($settings['title'] == 'required' && (($element['#delta'] == 0 && $element['#required']) || !empty($element['#value']['url']))) ? TRUE : FALSE,
      '#default_value' => isset($element['#value']['title']) ? $element['#value']['title'] : NULL,
    );
  }

  // Initialize field attributes as an array if it is not an array yet.
  if (!is_array($settings['attributes'])) {
    $settings['attributes'] = array();
  }
  // Add default attributes.
  $settings['attributes'] += _link_default_attributes();
  $attributes = isset($element['#value']['attributes']) ? $element['#value']['attributes'] : $settings['attributes'];
  if (!empty($settings['attributes']['target']) && $settings['attributes']['target'] == LINK_TARGET_USER) {
    $element['attributes']['target'] = array(
      '#type' => 'checkbox',
      '#title' => t('Open URL in a New Window'),
      '#return_value' => LINK_TARGET_NEW_WINDOW,
      '#default_value' => isset($attributes['target']) ? $attributes['target'] : FALSE,
    );
  }
  if (!empty($settings['attributes']['configurable_title']) && $settings['attributes']['configurable_title'] == 1) {
    $element['attributes']['title'] = array(
      '#type' => 'textfield',
      '#title' => t('Link "title" attribute'),
      '#default_value' => isset($attributes['title']) ? $attributes['title'] : '',
      '#field_prefix' => 'title = "',
      '#field_suffix' => '"',
    );
  }
  if (!empty($settings['attributes']['configurable_class']) && $settings['attributes']['configurable_class'] == 1) {
    $element['attributes']['class'] = array(
      '#type' => 'textfield',
      '#title' => t('Custom link class'),
      '#default_value' => isset($attributes['class']) ? $attributes['class'] : '',
      '#field_prefix' => 'class = "',
      '#field_suffix' => '"',
    );
  }

  // If the title field is avaliable or there are field accepts multiple values
  // then allow the individual field items display the required asterisk if needed.
  if (isset($element['title']) || isset($element['_weight'])) {
    // To prevent an extra required indicator, disable the required flag on the
    // base element since all the sub-fields are already required if desired.
    $element['#required'] = FALSE;
  }

  return $element;
}

/**
 * Override of theme_field_multiple_value_form.
 */
// function dgr_rubik_field_multiple_value_form($variables) {
//   $element = $variables['element'];
//   $output = '';

//   if ($element['#cardinality'] > 1 || $element['#cardinality'] == FIELD_CARDINALITY_UNLIMITED) {
//     $table_id = drupal_html_id($element['#field_name'] . '_values');
//     $order_class = $element['#field_name'] . '-delta-order';
//     $required = !empty($element['#required']) ? theme('form_required_marker', $variables) : '';

//     $header = array(
//       array(
//         'data' => '<label>' . t('!title !required', array('!title' => $element['#title'], '!required' => $required)) . "</label>" . '<div class="description">' . $element['#description'] . '</div>',
//         'colspan' => 2,
//         'class' => array('field-label'),
//       ),
//       t('Order'),
//     );
//     $rows = array();

//     // Sort items according to '_weight' (needed when the form comes back after
//     // preview or failed validation)
//     $items = array();
//     foreach (element_children($element) as $key) {
//       if ($key === 'add_more') {
//         $add_more_button = &$element[$key];
//       }
//       else {
//         $items[] = &$element[$key];
//       }
//     }
//     usort($items, '_field_sort_items_value_helper');

//     // Add the items as table rows.
//     foreach ($items as $key => $item) {
//       $item['_weight']['#attributes']['class'] = array($order_class);
//       $delta_element = drupal_render($item['_weight']);
//       $cells = array(
//         array('data' => '', 'class' => array('field-multiple-drag')),
//         drupal_render($item),
//         array('data' => $delta_element, 'class' => array('delta-order')),
//       );
//       $rows[] = array(
//         'data' => $cells,
//         'class' => array('draggable'),
//       );
//     }

//     $output = '<div class="form-item">';
//     $output .= theme('table', array('header' => $header, 'rows' => $rows, 'attributes' => array('id' => $table_id, 'class' => array('field-multiple-table'))));
//     $output .= '<div class="clearfix">' . drupal_render($add_more_button) . '</div>';
//     $output .= '</div>';

//     drupal_add_tabledrag($table_id, 'order', 'sibling', $order_class);
//   }
//   else {
//     foreach (element_children($element) as $key) {
//       $output .= drupal_render($element[$key]);
//     }
//   }

//   return $output;
// }

//Disable sticky headers
function dgr_rubik_js_alter(&$js) {
  unset($js['misc/tableheader.js']);

  foreach($js as $key=>$value) {
    if (strstr($key,'full_rubik.js') !== FALSE) {
      unset($js[$key]);  // prevent full_rubik.js from loading into the site
    }
  }
}

/**
 * Themes some exposed form elements in a collapsible fieldset.
 *
 * @param array $vars
 *   An array of arrays, the 'element' item holds the properties of the element.
 *
 * @return string
 *   HTML to render the form element.
 */
function dgr_rubik_secondary_exposed_elements($vars) {
  $element = $vars['element'];

  $output = '<div class="bef-secondary-options">';
  foreach (element_children($element) as $id) {
    if (in_array($id, $vars['widget_values'])) {
      // This item is a wiget value part.
      // Render entire widget here.

      $widget = $vars['widgets'][$vars['widget_value_id_map'][$id]];

      $output .= "<div id='{$widget->id}-wrapper' class='views-exposed-widget views-widget-$id'>";
      if (!empty($widget->label)) {
        $output .= "<label for='{$widget->id}'>{$widget->label}</label>";
      }
      if (!empty($widget->operator)) {
        $output .= "<div class='views-operator'>{$widget->operator}</div>";
      }
      $output .= "<div class='views-widget'>{$widget->widget}</div>";
      if (!empty($widget->description)) {
        $output .= "<div class='description'>{$widget->description}</div>";

      }
      $output .= "</div>";
    }
    // Need to avoid rendering widget parts like value and operator.
    // Then will be rendered as part of widget, within above if condition.
    else if (!in_array($id, $vars['widget_parts'])) {
      $output .= drupal_render($element[$id]);
    }
  }
  $output .= '</div>';

  return $output;
}

/**
 * Build widgets like template_preprocess_views_exposed_form()
 *
 * @see template_preprocess_views_exposed_form()
 */
function dgr_rubik_preprocess_secondary_exposed_elements(&$vars) {
  $element = $vars['element'];

  // Put all single checkboxes together in the last spot.
  $checkboxes = '';

  $vars['widgets'] = array();
  $vars['widget_parts'] = array();
  $vars['widget_values'] = array();
  $vars['widget_value_id_map'] = array();
  foreach ($element['#info'] as $id => $info) {
    // Set aside checkboxes.
    if (isset($element[$info['value']]['#type']) && $element[$info['value']]['#type'] == 'checkbox') {
      $checkboxes .= drupal_render($element[$info['value']]);
      continue;
    }
    $widget = new stdClass;
    // set up defaults so that there's always something there.
    $widget->label = $widget->operator = $widget->widget = $widget->description = NULL;

    $widget->id = isset($element[$info['value']]['#id']) ? $element[$info['value']]['#id'] : '';

    if (!empty($info['label'])) {
      $widget->label = check_plain($info['label']);
    }
    if (!empty($info['operator'])) {
      $operator = $element[$info['operator']];
      unset($operator['#title']);
      $widget->operator = drupal_render($operator);
      $vars['widget_parts'][] = $info['operator'];
    }

    $value = $element[$info['value']];
    unset($value['#title']);
    // Numberic input filter will be having extra 'min' and 'max' subfields along with 'value'
    // We don't need title for value field here.
    if (isset($value['value']) && isset($value['min']) && isset($value['max'])) {
      unset($value['value']['#title']);
    }
    $widget->widget = drupal_render($value);
    $vars['widget_parts'][] = $info['value'];
    $vars['widget_values'][] = $info['value'];
    $vars['widget_value_id_map'][$info['value']] = $id;

    if (!empty($info['description'])) {
      $widget->description = check_plain($info['description']);
    }

    $vars['widgets'][$id] = $widget;
  }
}

function dgr_rubik_preprocess_html(&$vars) {
  $viewport = array(
   '#tag' => 'meta',
   '#attributes' => array(
     'name' => 'viewport',
     'content' => 'width=device-width, initial-scale=1, user-scalable=yes',
   ),
  );
  drupal_add_html_head($viewport, 'viewport');
}

/**
* Increase the length of the slogan.
* Implements hook_form_FORM_ID_alter.
*/
function dgr_rubik_form_system_site_information_settings_alter(&$form, &$form_state, $form_id) {
  $form['site_information']['site_slogan']['#maxlength'] = 255;
}

/**
 * Implements hook_preprocess_page()
 */
function dgr_rubik_preprocess_page() {
  global $language;
  if ($language->language == 'es') {
    drupal_add_css(drupal_get_path('theme', 'dgr_rubik') . '/css/spanish.css', array('group' => CSS_THEME));
  }

  if (in_array(arg(0), array('articles', 'news', 'press-releases', 'faqs'))) { // Panel pages
    drupal_add_css(drupal_get_path('theme', 'dgr_rubik') . '/css/blog_pages.css', array('group' => CSS_THEME));
  }
  else if (arg(0) == 'categories') { // Categories view pages
    drupal_add_css(drupal_get_path('theme', 'dgr_rubik') . '/css/blog_pages.css', array('group' => CSS_THEME));
    drupal_add_css(drupal_get_path('theme', 'dgr_rubik') . '/css/categories-view-panel-pages.css', array('group' => CSS_THEME));
  }
  else if (arg(0) == 'taxonomy' && arg(1) == 'term' && preg_match('/^\d+$/', arg(2)) && empty(arg(3))) { // Categories term view pages
    drupal_add_css(drupal_get_path('theme', 'dgr_rubik') . '/css/category-term-pages.css', array('group' => CSS_THEME));
  }
  else if ((arg(0) == 'node' && preg_match('/^\d+$/', arg(1)) && empty(arg(2))) ) { // Node view page.
    drupal_add_css(drupal_get_path('theme', 'dgr_rubik') . '/css/blog_pages.css', array('group' => CSS_THEME));
    // Get node being displayed.
    $node = menu_get_object();
    if ($node->type == 'calendar_item') {
      drupal_add_css(drupal_get_path('theme', 'dgr_rubik') . '/css/calendar_item.css', array('group' => CSS_THEME));
    }
    else if (in_array($node->type, array('article_post', 'panopoly_news_article'))) {
      drupal_add_css(drupal_get_path('theme', 'dgr_rubik') . '/css/articles-news-nodes.css', array('group' => CSS_THEME));
    }
    else if ($node->type == 'press_release') {
      drupal_add_css(drupal_get_path('theme', 'dgr_rubik') . '/css/pres-release-nodes.css', array('group' => CSS_THEME));
    }
    else if ($node->type == 'panopoly_faq') {
      drupal_add_css(drupal_get_path('theme', 'dgr_rubik') . '/css/faq-nodes.css', array('group' => CSS_THEME));
    }
  }
  else if (arg(0) == 'user' && arg(1) == 'login') {
    drupal_add_css(drupal_get_path('theme', 'dgr_rubik') . '/css/login.css', array('group' => CSS_THEME));
  }
  else if (arg(0) == 'admin' && arg(1) == 'people' && arg(2) == 'create') {
    drupal_add_css(drupal_get_path('theme', 'dgr_rubik') . '/css/user_edit.css', array('group' => CSS_THEME));
  }
}

/**
 * Implements hook_preprocess_maintenance_page()
 */
function dgr_rubik_preprocess_maintenance_page() {
  drupal_add_css(drupal_get_path('theme', 'dgr_rubik') . '/css/maintenance.css', array('group' => CSS_THEME));
}

/**
 * Implements hook_form_BASE_FORM_ID_alter()
 */
function dgr_rubik_form_node_form_alter(&$form, &$form_state, $form_id) {
  $form['#attached']['css'][] = drupal_get_path('theme', 'dgr_rubik') . '/css/node_edit_forms.css';
}

/**
 * Implements hook_form_FORM_ID_alter()
 */
function dgr_rubik_form_user_profile_form_alter(&$form, &$form_state, $form_id) {
  $form['#attached']['css'][] = drupal_get_path('theme', 'dgr_rubik') . '/css/user_edit.css';
}

/**
 * Implements hook_user_view_alter()
 */
function dgr_rubik_user_view_alter($account, $view_mode, $langcode) {
  $attached_css = &drupal_static(__FUNCTION__);
  if (!isset($attached_css) || !$attached_css) {
    drupal_add_css(drupal_get_path('theme', 'dgr_rubik') . '/css/user.css', array('group' => CSS_THEME));
    $attached_css = TRUE;
  }
}

/**
 * Implements hook_views_pre_render()
 */
function dgr_rubik_views_pre_render(&$view) {

  if ($view->name == 'card_cycles') {
    drupal_add_css(drupal_get_path('theme', 'dgr_rubik') . '/css/view-card-cycles.css', array('group' => CSS_THEME));
  }
}
