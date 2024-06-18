<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => 'Поље :attribute мора бити прихваћено.',
    'accepted_if' => 'Поље :attribute мора бити прихваћено када је вредност поља :other :value.',
    'active_url' => 'Поље :attribute није валидан УРЛ.',
    'after' => 'Поље :attribute мора да буде датум после :date.',
    'after_or_equal' => 'Поље :attribute мора да буде датум једнак или после :date.',
    'alpha' => 'Поље :attribute може да садржи само слова.',
    'alpha_dash' => 'Поље :attribute може да садржи само слова, бројеве, повлаке и доње црте.',
    'alpha_num' => 'Поље :attribute може да садржи само слова и бројеве.',
    'array' => 'Поље :attribute мора да буде низ.',
    'before' => 'Поље :attribute мора да буде датум пре :date.',
    'before_or_equal' => 'Поље :attribute мора да буде датум једнак или пре :date.',
    'between' => [
        'numeric' => 'Поље :attribute мора да буде између :min и :max.',
        'file' => 'Поље :attribute мора да буде између :min и :max килобајта.',
        'string' => 'Поље :attribute мора да буде између :min и :max карактера.',
        'array' => 'Поље :attribute мора да садржи између :min и :max елемената.',
    ],
    'boolean' => 'Поље :attribute мора да буде тачно или нетачно.',
    'confirmed' => 'Потврда поља :attribute се не поклапа.',
    'current_password' => 'Лозинка је неисправна.',
    'date' => 'Поље :attribute није исправан датум.',
    'date_equals' => 'Поље :attribute мора да буде датум једнак :date.',
    'date_format' => 'Поље :attribute не одговара формату :format.',
    'declined' => 'Поље :attribute мора бити одбијено.',
    'declined_if' => 'Поље :attribute мора бити одбијено када је вредност поља :other :value.',
    'different' => 'Поља :attribute и :other морају бити различита.',
    'digits' => 'Поље :attribute мора садржати :digits цифре.',
    'digits_between' => 'Поље :attribute мора да садржи између :min и :max цифри.',
    'dimensions' => 'Поље :attribute садржи недозвољене димензије слике.',
    'distinct' => 'Поље :attribute садржи дуплирану вредност.',
    'email' => 'Поље :attribute мора да буде исправна е-мејл адреса.',
    'ends_with' => 'Поље :attribute мора да се заврши са једном од следећих вредности: :values.',
    'enum' => 'Изабрано поље :attribute је неисправно.',
    'exists' => 'Изабрано поље :attribute је неисправно.',
    'file' => 'Поље :attribute мора да буде фајл.',
    'filled' => 'Поље :attribute мора да садржи вредност.',
    'gt' => [
        'numeric' => 'Поље :attribute мора да буде веће од :value.',
        'file' => 'Поље :attribute мора да буде веће од :value килобајта.',
        'string' => 'Поље :attribute мора да садржи више од :value карактера.',
        'array' => 'Поље :attribute мора да садржи више од :value елемената.',
    ],
    'gte' => [
        'numeric' => 'Поље :attribute мора да буде веће или једнако од :value.',
        'file' => 'Поље :attribute мора да буде веће или једнако од :value килобајта.',
        'string' => 'Поље :attribute мора да садржи тачно или више од :value карактера.',
        'array' => 'Поље :attribute мора да садржи :value или више елемената.',
    ],
    'image' => 'Поље :attribute мора да буде слика.',
    'in' => 'Изабрано поље :attribute је неисправно.',
    'in_array' => 'Поље :attribute се не налази у :other.',
    'integer' => 'Поље :attribute мора да буде број.',
    'ip' => 'Поље :attribute мора да буде исправна ИП адреса.',
    'ipv4' => 'Поље :attribute мора да буде исправна ИПв4 адреса.',
    'ipv6' => 'Поље :attribute мора да буде исправна ИПв6 адреса.',
    'json' => 'Поље :attribute мора да буде исправан ЈСОН формат.',
    'lt' => [
        'numeric' => 'Поље :attribute мора да буде мање од :value.',
        'file' => 'Поље :attribute мора да буде мање од :value килобајта.',
        'string' => 'Поље :attribute мора да садржи мање од :value карактера.',
        'array' => 'Поље :attribute мора да садржи мање од :value елемената.',
    ],
    'lte' => [
        'numeric' => 'Поље :attribute мора да буде мање или једнако од :value.',
        'file' => 'Поље :attribute мора да буде мање или једнако од :value килобајта.',
        'string' => 'Поље :attribute мора да садржи тачно или мање од  :value карактера.',
        'array' => 'Поље :attribute мора да садржи тачно или мање од :value елемента.',
    ],
    'mac_address' => 'Поље :attribute мора да буде исправна МАЦ адреса.',
    'max' => [
        'numeric' => 'Поље :attribute мора да буде мање од :max.',
        'file' => 'Поље :attribute мора да буде мање од :max килобајта.',
        'string' => 'Поље :attribute мора да садржи мање од :max карактера.',
        'array' => 'Поље :attribute мора да садржи мање од :max елемената.',
    ],
    'mimes' => 'Поље :attribute мора да буде фајл типа: :values.',
    'mimetypes' => 'Поље :attribute мора да буде фајл типа: :values.',
    'min' => [
        'numeric' => 'Поље :attribute мора да буде најмање :min.',
        'file' => 'Поље :attribute мора да буде најмање :min килобајта.',
        'string' => 'Поље :attribute мора да садржи најмање :min карактера.',
        'array' => 'Поље :attribute мора да садржи најмање :min елемената.',
    ],
    'multiple_of' => 'Поље :attribute мора да буде вишеструко од :value.',
    'not_in' => 'Изабрано поље :attribute је неисправно.',
    'not_regex' => 'Формат поља :attribute  је неисправан.',
    'numeric' => 'Поље :attribute мора да буде број.',
    'password' => 'Лозинка је нетачна.',
    'present' => 'Поље :attribute мора да буде присутно.',
    'prohibited' => 'Поље :attribute је забрањено.',
    'prohibited_if' => 'Поље :attribute је забрањено када је вредност поља :other :value.',
    'prohibited_unless' => 'Поље :attribute је забрањено осим ако је вредност поља :other :values.',
    'prohibits' => 'Поље :attribute забрањује пољу :other да буде присутно.',
    'regex' => 'Формат поља :attribute је неисправан.',
    'required' => 'Поље :attribute је обавезно.',
    'required_array_keys' => 'Поље :attribute мора да садржи уносе за: :values.',
    'required_if' => 'Поље :attribute је обавезно када је вредност поља :other :value.',
    'required_unless' => 'Поље :attribute је обавезно осим ако је вредност поља :other :values.',
    'required_with' => 'Поље :attribute је обавезно када је поље :values присутно.',
    'required_with_all' => 'Поље :attribute је обавезно када су поља :values присутна.',
    'required_without' => 'Поље :attribute је обавезно када поља :values нису присутна.',
    'required_without_all' => 'Поље :attribute је обавезно када ниједно од поља :values није присутно.',
    'same' => 'Поља :attribute и :other се не поклапају.',
    'size' => [
        'numeric' => 'Поље :attribute mora da bude :size.',
        'file' => 'Поље :attribute мора да буде :size килобајта.',
        'string' => 'Поље :attribute мора да садржи :size карактера.',
        'array' => 'Поље :attribute мора да садржи :size елемента.',
    ],
    'starts_with' => 'Поље :attribute мора да почне са једним од следећих вредности: :values.',
    'string' => 'Поље :attribute мора да буде текст.',
    'timezone' => 'Поље :attribute мора да буде исправна временска зона.',
    'unique' => 'Поље :attribute је већ заузето.',
    'uploaded' => 'Поље :attribute није отпремљено.',
    'url' => 'Поље :attribute мора да буде исправан УРЛ.',
    'uuid' => 'Поље :attribute мора да буде исправан УУИД.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [],

];