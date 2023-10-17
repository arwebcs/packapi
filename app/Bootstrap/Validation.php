<?php

namespace App\Bootstrap;

use App\Database\Query;

class Validation
{
    private $data;
    private $errors;

    public function __construct($data)
    {
        $this->data = $data;
        $this->errors = [];
    }
    public function validateRules($rules = array())
    {
        foreach ($rules as $field => $valid) {
            foreach ($valid as $key => $message) {
                $this->addRule($field, $key, $message);
            }
        }
    }
    public function addRule($field, $rule, $message)
    {
        if (!isset($this->data[$field])) {
            $this->errors[$field][] = "Field is missing.";
            return;
        }

        if (str_contains($rule, ":")) {
            $rule = preg_replace('/\s/', '', $rule);
            $value = explode(":", $rule)[1];
            $rule = explode(":", $rule)[0];
        }

        switch ($rule) {

                /**************************** Extra validations ****************************/

            case 'required':
                if (empty($this->data[$field]) && !($this->data[$field] == 0)) {
                    $this->errors[$field][] = $message;
                }
                break;

            case 'email':
                if (!empty($this->data[$field])) {
                    if (!filter_var($this->data[$field], FILTER_VALIDATE_EMAIL)) {
                        $this->errors[$field][] = $message;
                    }
                }
                break;

            case 'url':
                if (!empty($this->data[$field])) {
                    if (!preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i", $this->data[$field])) {
                        $this->errors[$field][] = $message;
                    }
                }
                break;

            case 'boolean':
                if (!empty($this->data[$field])) {
                    if (!is_bool($this->data[$field])) {
                        $this->errors[$field][] = $message;
                    }
                }
                break;

            case 'ip':
                if (!empty($this->data[$field])) {
                    if (empty($value)) {
                        if (filter_var($this->data[$field], FILTER_VALIDATE_IP)) {
                            $this->errors[$field][] = $message;
                        }
                    } else {
                        switch ($value) {
                            case "v4":
                                if (filter_var($this->data[$field], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                                    $this->errors[$field][] = $message;
                                }
                                break;
                            case "v6":
                                if (filter_var($this->data[$field], FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
                                    $this->errors[$field][] = $message;
                                }
                                break;
                            default:
                                break;
                        }
                    }
                }
                break;


                /**************************** Extra validations ****************************/

                /**************************** String validations ****************************/

            case 'uppercase':
                if (!empty($this->data[$field])) {
                    if (!ctype_upper($this->data[$field])) {
                        $this->errors[$field][] = $message;
                    }
                }
                break;

            case 'lowercase':
                if (!empty($this->data[$field])) {
                    if (!ctype_lower($this->data[$field])) {
                        $this->errors[$field][] = $message;
                    }
                }
                break;

            case 'same':
                if (!empty($this->data[$field])) {
                    $value = preg_replace('/\s/', '', $value);
                    if (!(strcmp($this->data[$field], $this->data[$value]) == 0)) {
                        $this->errors[$field][] = $message;
                    }
                }
                break;

            case 'different':
                if (!empty($this->data[$field])) {
                    $value = preg_replace('/\s/', '', $value);
                    if (strcmp($this->data[$field], $this->data[$value]) == 0) {
                        $this->errors[$field][] = $message;
                    }
                }
                break;

            case 'pattern':
                if (!empty($this->data[$field])) {
                    if (!preg_match("/^[$value]*$/", $this->data[$field])) {
                        $this->errors[$field][] = $message;
                    }
                }
                break;

            case 'strlength':
                if (!empty($this->data[$field])) {
                    if (!(strlen($this->data[$field]) == $value)) {
                        $this->errors[$field][] = $message;
                    }
                }
                break;

            case 'minlength':
                if (!empty($this->data[$field])) {
                    if (strlen($this->data[$field]) < $value) {
                        $this->errors[$field][] = $message;
                    }
                }
                break;

            case 'maxlength':
                if (!empty($this->data[$field])) {
                    if (strlen($this->data[$field]) > $value) {
                        $this->errors[$field][] = $message;
                    }
                }
                break;

                /**************************** String validations ****************************/

                /**************************** Number validations ****************************/

            case 'numeric':
                if (!empty($this->data[$field])) {
                    if (!is_numeric($this->data[$field])) {
                        $this->errors[$field][] = $message;
                    }
                }
                break;

            case 'integer':
                if (!empty($this->data[$field])) {
                    if (!is_int($this->data[$field])) {
                        $this->errors[$field][] = $message;
                    }
                }
                break;

            case 'float':
                if (!empty($this->data[$field])) {
                    if (!is_float($this->data[$field])) {
                        $this->errors[$field][] = $message;
                    }
                }
                break;

            case 'digits-between':
                if (!empty($this->data[$field])) {
                    $value = preg_replace('/\s/', '', $value);
                    $arrValue = explode(",", $value);
                    $fno = $arrValue[0];
                    $sno = $arrValue[1];
                    if (!($this->data[$field] > $fno && $this->data[$field] < $sno)) {
                        $this->errors[$field][] = $message;
                    }
                }
                break;

            case 'number-not-equal':
                if (!empty($this->data[$field])) {
                    if ($this->data[$field] == $value) {
                        $this->errors[$field][] = $message;
                    }
                }
                break;

            case 'number-equal':
                if (!empty($this->data[$field])) {
                    if (!($this->data[$field] == $value)) {
                        $this->errors[$field][] = $message;
                    }
                }
                break;

            case 'lt':
                if (!empty($this->data[$field])) {
                    if ($this->data[$field] >= $value) {
                        $this->errors[$field][] = $message;
                    }
                }
                break;

            case 'gt':
                if (!empty($this->data[$field])) {
                    if ($this->data[$field] <= $value) {
                        $this->errors[$field][] = $message;
                    }
                }
                break;

            case 'lte':
                if (!empty($this->data[$field])) {
                    if ($this->data[$field] > $value) {
                        $this->errors[$field][] = $message;
                    }
                }
                break;

            case 'gte':
                if (!empty($this->data[$field])) {
                    if ($this->data[$field] < $value) {
                        $this->errors[$field][] = $message;
                    }
                }
                break;

                /**************************** Number validations ****************************/

                /**************************** File validations ****************************/

            case "file-required":
                if ($this->data[$field]['size'] == 0) {
                    $this->errors[$field][] = $message;
                }
                break;

            case "file-min-size":
                if ($this->data[$field]['size'] > 0) {
                    if ($this->data[$field]['size'] < $value) {
                        $this->errors[$field][] = $message;
                    }
                }
                break;

            case "file-max-size":
                if ($this->data[$field]['size'] > 0) {
                    if ($this->data[$field]['size'] > $value) {
                        $this->errors[$field][] = $message;
                    }
                }
                break;

            case "file-mime-type":
                if ($this->data[$field]['size'] > 0) {
                    $value = preg_replace('/\s/', '', $value);
                    if (!in_array($this->data[$field]['type'], explode(",", $value))) {
                        $this->errors[$field][] = $message;
                    }
                }
                break;

                /**************************** File validations ****************************/

                /**************************** Array/JSON validations ****************************/

            case 'array-required':
                if (sizeof($this->data[$field]) == 0) {
                    $this->errors[$field][] = $message;
                }
                break;

            case 'array':
                if (!is_array($this->data[$field])) {
                    $this->errors[$field][] = $message;
                }
                break;

            case 'in':
                $value = preg_replace('/\s/', '', $value);
                if (!in_array($this->data[$field], explode(",", $value))) {
                    $this->errors[$field][] = $message;
                }
                break;

            case 'not-in':
                $value = preg_replace('/\s/', '', $value);
                if (in_array($this->data[$field], explode(",", $value))) {
                    $this->errors[$field][] = $message;
                }
                break;

            case 'json':
                if (!is_string($this->data[$field]) && is_array(json_decode($this->data[$field], true))) {
                    $this->errors[$field][] = $message;
                }
                break;

                /**************************** Array/JSON validations ****************************/

                /**************************** Database validations ****************************/

            case 'db-exist':
                if (!empty($this->data[$field])) {
                    $value = preg_replace('/\s/', '', $value);
                    $arrValue = explode(",", $value);
                    $table = $arrValue[0];
                    $fieldName = $arrValue[1];
                    $sql = Query::table($table)
                        ->select()
                        ->where("$table.$fieldName=?", [$this->data[$field]]);

                    $count = $sql->count();
                    if ($count == 0) {
                        $this->errors[$field][] = $message;
                    }
                }
                break;

            case 'db-unique':
                if (!empty($this->data[$field])) {
                    $value = preg_replace('/\s/', '', $value);
                    $arrValue = explode(",", $value);
                    $table = $arrValue[0];
                    $fieldName = $arrValue[1];
                    $sql = Query::table($table)
                        ->select()
                        ->where("$table.$fieldName=?", [$this->data[$field]]);
                    $count = $sql->count();

                    if ($count > 0) {
                        $this->errors[$field][] = $message;
                    }
                }
                break;

            case 'db-unique-except':
                if (!empty($this->data[$field])) {
                    $value = preg_replace('/\s/', '', $value);
                    $arrValue = explode(",", $value);
                    $table = $arrValue[0];
                    $fieldName = $arrValue[1];
                    $exceptValue = preg_replace('/\s/', '', $arrValue[2]);
                    $arrExcept = explode("-", $exceptValue);
                    $exceptFieldName = $arrExcept[0];
                    $exceptFieldValue = $arrExcept[1];

                    $sql = Query::table($table)
                        ->select()
                        ->where(
                            "$table.$fieldName=? AND $table.$exceptFieldName!=?",
                            [$this->data[$field], $exceptFieldValue]
                        );

                    $count = $sql->count();

                    if ($count > 0) {
                        $this->errors[$field][] = $message;
                    }
                }
                break;

                /**************************** Database validations ****************************/

            default:
                break;
        }
    }

    public function validate()
    {
        return empty($this->errors);
    }

    public function getErrors()
    {
        return $this->errors;
    }
}
