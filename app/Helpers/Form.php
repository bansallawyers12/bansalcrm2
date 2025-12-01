<?php

namespace App\Helpers;

use Spatie\Html\Facades\Html;

class Form
{
    /**
     * Open a form tag
     */
    public static function open($options = [])
    {
        $attributes = [];
        $url = null;
        $method = 'POST';
        
        if (isset($options['url'])) {
            $url = $options['url'];
            unset($options['url']);
        }
        
        if (isset($options['method'])) {
            $method = strtoupper($options['method']);
            unset($options['method']);
        }
        
        // Handle novalidate attribute properly (boolean attribute)
        if (isset($options['novalidate']) && $options['novalidate']) {
            $form = Html::form()->novalidate();
            unset($options['novalidate']);
        } else {
            $form = Html::form();
        }
        
        // Convert array attributes to string format
        foreach ($options as $key => $value) {
            $attributes[$key] = $value;
        }
        
        if ($url) {
            $form->action($url);
        }
        
        $form->method($method);
        
        if (!empty($attributes)) {
            $form->attributes($attributes);
        }
        
        return $form->open();
    }
    
    /**
     * Close a form tag
     */
    public static function close()
    {
        return Html::form()->close();
    }
    
    /**
     * Create a text input field
     */
    public static function text($name, $value = null, $options = [])
    {
        $attributes = is_array($options) ? $options : [];
        
        // Remove HTML5 'required' attribute if present
        if (isset($attributes['required'])) {
            unset($attributes['required']);
        }
        
        // Ensure spellcheck is disabled
        if (!isset($attributes['spellcheck'])) {
            $attributes['spellcheck'] = 'false';
        }
        
        // Build HTML manually to avoid Spatie HTML rendering issues in Laravel 12
        $html = '<input type="text" name="' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . '"';
        
        if ($value !== null && $value !== '') {
            $html .= ' value="' . htmlspecialchars($value, ENT_QUOTES, 'UTF-8') . '"';
        }
        
        foreach ($attributes as $key => $val) {
            if ($val !== null && $val !== false) {
                if (is_bool($val) && $val === true) {
                    $html .= ' ' . htmlspecialchars($key, ENT_QUOTES, 'UTF-8');
                } else {
                    $html .= ' ' . htmlspecialchars($key, ENT_QUOTES, 'UTF-8') . '="' . htmlspecialchars($val, ENT_QUOTES, 'UTF-8') . '"';
                }
            }
        }
        
        $html .= '>';
        
        return $html;
    }
    
    /**
     * Create a number input field
     */
    public static function number($name, $value = null, $options = [])
    {
        $attributes = is_array($options) ? $options : [];
        
        $input = Html::input('number', $name, $value);
        
        if (!empty($attributes)) {
            $input->attributes($attributes);
        }
        
        return $input->toHtml();
    }
    
    /**
     * Create a hidden input field
     */
    public static function hidden($name, $value = null, $options = [])
    {
        $attributes = is_array($options) ? $options : [];
        
        $input = Html::input('hidden', $name, $value);
        
        if (!empty($attributes)) {
            $input->attributes($attributes);
        }
        
        return $input->toHtml();
    }
    
    /**
     * Create a submit button
     */
    public static function submit($value = null, $options = [])
    {
        $attributes = is_array($options) ? $options : [];
        
        if ($value === null) {
            $value = 'Submit';
        }
        
        $button = Html::button($value)->type('submit');
        
        if (!empty($attributes)) {
            $button->attributes($attributes);
        }
        
        return $button->toHtml();
    }
    
    /**
     * Create a button element
     */
    public static function button($value = null, $options = [])
    {
        $attributes = is_array($options) ? $options : [];
        
        if ($value === null) {
            $value = 'Button';
        }
        
        $button = Html::button($value);
        
        if (!empty($attributes)) {
            $button->attributes($attributes);
        }
        
        return $button->toHtml();
    }
    
    /**
     * Create a textarea field
     */
    public static function textarea($name, $value = null, $options = [])
    {
        $attributes = is_array($options) ? $options : [];
        
        // Remove HTML5 'required' attribute if present
        if (isset($attributes['required'])) {
            unset($attributes['required']);
        }
        
        // Ensure spellcheck is disabled
        if (!isset($attributes['spellcheck'])) {
            $attributes['spellcheck'] = 'false';
        }
        
        // Build HTML manually to avoid Spatie HTML rendering issues in Laravel 12
        $html = '<textarea name="' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . '"';
        
        foreach ($attributes as $key => $val) {
            if ($val !== null && $val !== false) {
                if (is_bool($val) && $val === true) {
                    $html .= ' ' . htmlspecialchars($key, ENT_QUOTES, 'UTF-8');
                } else {
                    $html .= ' ' . htmlspecialchars($key, ENT_QUOTES, 'UTF-8') . '="' . htmlspecialchars($val, ENT_QUOTES, 'UTF-8') . '"';
                }
            }
        }
        
        $html .= '>';
        
        if ($value !== null) {
            $html .= htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        }
        
        $html .= '</textarea>';
        
        return $html;
    }
    
    /**
     * Create a select dropdown
     */
    public static function select($name, $list = [], $selected = null, $options = [])
    {
        $attributes = is_array($options) ? $options : [];
        
        $select = Html::select()->name($name);
        
        if (!empty($attributes)) {
            $select->attributes($attributes);
        }
        
        foreach ($list as $key => $label) {
            $option = Html::option($label)->value($key);
            if ($selected !== null && (string)$key === (string)$selected) {
                $option->selected();
            }
            $select->addChild($option);
        }
        
        return $select->toHtml();
    }
    
    /**
     * Create a checkbox input
     */
    public static function checkbox($name, $value = 1, $checked = false, $options = [])
    {
        $attributes = is_array($options) ? $options : [];
        
        $input = Html::input('checkbox', $name, $value);
        
        if ($checked) {
            $input->checked();
        }
        
        if (!empty($attributes)) {
            $input->attributes($attributes);
        }
        
        return $input->toHtml();
    }
    
    /**
     * Create a radio button input
     */
    public static function radio($name, $value = null, $checked = false, $options = [])
    {
        $attributes = is_array($options) ? $options : [];
        
        $input = Html::input('radio', $name, $value);
        
        if ($checked) {
            $input->checked();
        }
        
        if (!empty($attributes)) {
            $input->attributes($attributes);
        }
        
        return $input->toHtml();
    }
    
    /**
     * Create a file input
     */
    public static function file($name, $options = [])
    {
        $attributes = is_array($options) ? $options : [];
        
        $input = Html::input('file', $name);
        
        if (!empty($attributes)) {
            $input->attributes($attributes);
        }
        
        return $input->toHtml();
    }
    
    /**
     * Create a label element
     */
    public static function label($name, $value = null, $options = [])
    {
        $attributes = is_array($options) ? $options : [];
        
        if ($value === null) {
            $value = ucfirst(str_replace('_', ' ', $name));
        }
        
        $label = Html::label($value);
        
        if (isset($attributes['for'])) {
            $label->for($attributes['for']);
            unset($attributes['for']);
        }
        
        if (!empty($attributes)) {
            $label->attributes($attributes);
        }
        
        return $label->toHtml();
    }
    
    /**
     * Create a time input field
     */
    public static function time($name, $value = null, $options = [])
    {
        $attributes = is_array($options) ? $options : [];
        
        $input = Html::input('time', $name, $value);
        
        if (!empty($attributes)) {
            $input->attributes($attributes);
        }
        
        return $input->toHtml();
    }
    
    /**
     * Create a date input field
     */
    public static function date($name, $value = null, $options = [])
    {
        $attributes = is_array($options) ? $options : [];
        
        $input = Html::input('date', $name, $value);
        
        if (!empty($attributes)) {
            $input->attributes($attributes);
        }
        
        return $input->toHtml();
    }
}

