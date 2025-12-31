<?php

namespace App\Helpers;

class Form
{
    /**
     * Open a form tag
     * 
     * Manually builds the HTML form tag to ensure proper rendering of method and action attributes.
     * Supports both 'url' and 'route' parameters for Laravel compatibility.
     */
    public static function open($options = [])
    {
        $attributes = [];
        $url = null;
        $method = 'POST';
        
        // Handle URL or route
        if (isset($options['url'])) {
            $url = url($options['url']);
            unset($options['url']);
        } elseif (isset($options['route'])) {
            $url = route($options['route']);
            unset($options['route']);
        }
        
        // Handle method
        if (isset($options['method'])) {
            $method = strtoupper($options['method']);
            unset($options['method']);
        }
        
        // Store remaining options as attributes
        foreach ($options as $key => $value) {
            $attributes[$key] = $value;
        }
        
        // Manually build the form tag HTML
        $html = '<form';
        
        // Add action attribute
        if ($url) {
            $html .= ' action="' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . '"';
        }
        
        // Add method attribute (critical - this is what Spatie HTML fails to render)
        $html .= ' method="' . htmlspecialchars($method, ENT_QUOTES, 'UTF-8') . '"';
        
        // Add other attributes
        foreach ($attributes as $key => $value) {
            if ($value !== null && $value !== false) {
                if (is_bool($value) && $value === true) {
                    // Boolean attributes (e.g., novalidate)
                    $html .= ' ' . htmlspecialchars($key, ENT_QUOTES, 'UTF-8');
                } else {
                    // Regular attributes
                    $html .= ' ' . htmlspecialchars($key, ENT_QUOTES, 'UTF-8') . '="' . htmlspecialchars($value, ENT_QUOTES, 'UTF-8') . '"';
                }
            }
        }
        
        $html .= '>';
        
        // Add CSRF token for POST/PUT/PATCH/DELETE requests
        if (in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            $html .= csrf_field();
        }
        
        // Add method spoofing for PUT/PATCH/DELETE
        if (in_array($method, ['PUT', 'PATCH', 'DELETE'])) {
            $html .= method_field($method);
        }
        
        return $html;
    }
    
    /**
     * Close a form tag
     */
    public static function close()
    {
        return '</form>';
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
        
        // Build HTML manually
        $html = '<input type="number" name="' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . '"';
        
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
     * Create a hidden input field
     */
    public static function hidden($name, $value = null, $options = [])
    {
        $attributes = is_array($options) ? $options : [];
        
        // Build HTML manually
        $html = '<input type="hidden" name="' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . '"';
        
        if ($value !== null) {
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
     * Create a submit button
     */
    public static function submit($value = null, $options = [])
    {
        $attributes = is_array($options) ? $options : [];
        
        if ($value === null) {
            $value = 'Submit';
        }
        
        // Build HTML manually
        $html = '<button type="submit"';
        
        foreach ($attributes as $key => $val) {
            if ($val !== null && $val !== false) {
                if (is_bool($val) && $val === true) {
                    $html .= ' ' . htmlspecialchars($key, ENT_QUOTES, 'UTF-8');
                } else {
                    $html .= ' ' . htmlspecialchars($key, ENT_QUOTES, 'UTF-8') . '="' . htmlspecialchars($val, ENT_QUOTES, 'UTF-8') . '"';
                }
            }
        }
        
        $html .= '>' . htmlspecialchars($value, ENT_QUOTES, 'UTF-8') . '</button>';
        
        return $html;
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
        
        // Build HTML manually
        $html = '<button';
        
        foreach ($attributes as $key => $val) {
            if ($val !== null && $val !== false) {
                if (is_bool($val) && $val === true) {
                    $html .= ' ' . htmlspecialchars($key, ENT_QUOTES, 'UTF-8');
                } else {
                    $html .= ' ' . htmlspecialchars($key, ENT_QUOTES, 'UTF-8') . '="' . htmlspecialchars($val, ENT_QUOTES, 'UTF-8') . '"';
                }
            }
        }
        
        $html .= '>' . htmlspecialchars($value, ENT_QUOTES, 'UTF-8') . '</button>';
        
        return $html;
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
        
        // Build HTML manually
        $html = '<select name="' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . '"';
        
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
        
        foreach ($list as $key => $label) {
            $html .= '<option value="' . htmlspecialchars($key, ENT_QUOTES, 'UTF-8') . '"';
            if ($selected !== null && (string)$key === (string)$selected) {
                $html .= ' selected';
            }
            $html .= '>' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</option>';
        }
        
        $html .= '</select>';
        
        return $html;
    }
    
    /**
     * Create a checkbox input
     */
    public static function checkbox($name, $value = 1, $checked = false, $options = [])
    {
        $attributes = is_array($options) ? $options : [];
        
        // Build HTML manually
        $html = '<input type="checkbox" name="' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . '" value="' . htmlspecialchars($value, ENT_QUOTES, 'UTF-8') . '"';
        
        if ($checked) {
            $html .= ' checked';
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
     * Create a radio button input
     */
    public static function radio($name, $value = null, $checked = false, $options = [])
    {
        $attributes = is_array($options) ? $options : [];
        
        // Build HTML manually
        $html = '<input type="radio" name="' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . '"';
        
        if ($value !== null) {
            $html .= ' value="' . htmlspecialchars($value, ENT_QUOTES, 'UTF-8') . '"';
        }
        
        if ($checked) {
            $html .= ' checked';
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
     * Create a file input
     */
    public static function file($name, $options = [])
    {
        $attributes = is_array($options) ? $options : [];
        
        // Build HTML manually
        $html = '<input type="file" name="' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . '"';
        
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
     * Create a label element
     */
    public static function label($name, $value = null, $options = [])
    {
        $attributes = is_array($options) ? $options : [];
        
        if ($value === null) {
            $value = ucfirst(str_replace('_', ' ', $name));
        }
        
        // Build HTML manually
        $html = '<label';
        
        if (isset($attributes['for'])) {
            $html .= ' for="' . htmlspecialchars($attributes['for'], ENT_QUOTES, 'UTF-8') . '"';
            unset($attributes['for']);
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
        
        $html .= '>' . htmlspecialchars($value, ENT_QUOTES, 'UTF-8') . '</label>';
        
        return $html;
    }
    
    /**
     * Create a time input field
     */
    public static function time($name, $value = null, $options = [])
    {
        $attributes = is_array($options) ? $options : [];
        
        // Build HTML manually
        $html = '<input type="time" name="' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . '"';
        
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
     * Create a date input field
     */
    public static function date($name, $value = null, $options = [])
    {
        $attributes = is_array($options) ? $options : [];
        
        // Build HTML manually
        $html = '<input type="date" name="' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . '"';
        
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
}

