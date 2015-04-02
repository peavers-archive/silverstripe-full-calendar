<?php

/**
 * Class HexColorField
 * @package: full-calendar
 */
class HexColorField extends TextField
{

    /**
     * Ensures the text field looks the same as current fields
     *
     * @return string
     */
    public function Type()
    {
        return 'text';
    }

    /**
     * Makes sure a correct hex value is loaded into this field, accept either 3 or 6 character codes.
     *
     * @param $validator
     * @return bool
     */
    public function validate($validator)
    {
        if (!preg_match('/#([a-f]|[A-F]|[0-9]){3}(([a-f]|[A-F]|[0-9]){3})?\b/', $this->value)) {
            $validator->validationError(
                $this->name, "<strong>ERROR:</strong> This is not a valid hex code. Should be either 3 or 6 characters.", "validation", false
            );

            return false;
        }

        return true;
    }
}
