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

    'accepted' => 'Polje :attribute mora biti prihvaćeno.',
    'accepted_if' => 'Polje :attribute mora biti prihvaćeno kada je vrednost polja :other :value.',
    'active_url' => 'Polje :attribute nije validan URL.',
    'after' => 'Polje :attribute mora da bude datum posle :date.',
    'after_or_equal' => 'Polje :attribute mora da bude datum jednak ili posle :date.',
    'alpha' => 'Polje :attribute može da sadrži samo slova.',
    'alpha_dash' => 'Polje :attribute može da sadrži samo slova, brojeve, povlake i donje crte.',
    'alpha_num' => 'Polje :attribute može da sadrži samo slova i brojeve.',
    'array' => 'Polje :attribute mora da bude niz.',
    'before' => 'Polje :attribute mora da bude datum pre :date.',
    'before_or_equal' => 'Polje :attribute mora da bude datum jednak ili pre :date.',
    'between' => [
        'numeric' => 'Polje :attribute mora da bude između :min i :max.',
        'file' => 'Polje :attribute mora da bude između :min i :max kilobajta.',
        'string' => 'Polje :attribute mora da bude između :min i :max karaktera.',
        'array' => 'Polje :attribute mora da sadrži između :min i :max elemenata.',
    ],
    'boolean' => 'Polje :attribute mora da bude tačno ili netačno.',
    'confirmed' => 'Potvrda polja :attribute se ne poklapa.',
    'current_password' => 'Lozinka je neispravna.',
    'date' => 'Polje :attribute nije ispravan datum.',
    'date_equals' => 'Polje :attribute mora da bude datum jednak :date.',
    'date_format' => 'Polje :attribute ne odgovara formatu :format.',
    'declined' => 'Polje :attribute mora biti odbijeno.',
    'declined_if' => 'Polje :attribute mora biti odbijeno kada je vrednost polja :other :value.',
    'different' => 'Polja :attribute :other moraju biti različita.',
    'digits' => 'Polje :attribute mora sadržati :digits cifre.',
    'digits_between' => 'Polje :attribute mora da sadrži između :min i :max cifri.',
    'dimensions' => 'Polje :attribute sadrži nedozvoljene dimenzije slike.',
    'distinct' => 'Polje :attribute sadrži dupliranu vrednost.',
    'email' => 'Polje :attribute mora da bude ispravna e-mejl adresa.',
    'ends_with' => 'Polje :attribute mora da se završi sa jednom od sledećih vrednosti: :values.',
    'enum' => 'Izabrano polje :attribute je neispravno.',
    'exists' => 'Izabrano polje :attribute je neispravno.',
    'file' => 'Polje :attribute mora da bude fajl.',
    'filled' => 'Polje :attribute mora da sadrži vrednost.',
    'gt' => [
        'numeric' => 'Polje :attribute mora da bude veće od :value.',
        'file' => 'Polje :attribute mora da bude veće od :value kilobajta.',
        'string' => 'Polje :attribute mora da sadrži više od :value karaktera.',
        'array' => 'Polje :attribute mora da sadrži više od :value elemenata.',
    ],
    'gte' => [
        'numeric' => 'Polje :attribute mora da bude veće ili jednako od :value.',
        'file' => 'Polje :attribute mora da bude veće ili jednako od :value kilobajta.',
        'string' => 'Polje :attribute mora da sadrži tačno ili više od :value karaktera.',
        'array' => 'Polje :attribute mora da sadrži :value ili više elemenata.',
    ],
    'image' => 'Polje :attribute mora da bude slika.',
    'in' => 'Izabrano polje :attribute je neispravno.',
    'in_array' => 'Polje :attribute se ne nalazi u :other.',
    'integer' => 'Polje :attribute mora da bude broj.',
    'ip' => 'Polje :attribute mora da bude ispravna IP adresa.',
    'ipv4' => 'Polje :attribute mora da bude ispravna IPv4 adresa.',
    'ipv6' => 'Polje :attribute mora da bude ispravna IPv6 adresa.',
    'json' => 'Polje :attribute mora da bude ispravan JSON format.',
    'lt' => [
        'numeric' => 'Polje :attribute mora da bude manje od :value.',
        'file' => 'Polje :attribute mora da bude manje od :value kilobajta.',
        'string' => 'Polje :attribute mora da sadrži manje od :value karaktera.',
        'array' => 'Polje :attribute mora da sadrži manje od :value elemenata.',
    ],
    'lte' => [
        'numeric' => 'Polje :attribute mora da bude manje ili jednako od :value.',
        'file' => 'Polje :attribute mora da bude manje ili jednako od :value kilobajta.',
        'string' => 'Polje :attribute mora da sadrži tačno ili manje od :value karaktera.',
        'array' => 'Polje :attribute mora da sadrži tačno ili manje od :value elementa.',
    ],
    'mac_address' => 'Polje :attribute mora da bude ispravna MAC adresa.',
    'max' => [
        'numeric' => 'Polje :attribute mora da bude manje od :max.',
        'file' => 'Polje :attribute mora da bude manje od :max kilobajta.',
        'string' => 'Polje :attribute mora da sadrži manje od :max karaktera.',
        'array' => 'Polje :attribute mora da sadrži manje od :max elemenata.',
    ],
    'mimes' => 'Polje :attribute mora da bude fajl tipa: :values.',
    'mimetypes' => 'Polje :attribute mora da bude fajl tipa: :values.',
    'min' => [
        'numeric' => 'Polje :attribute mora da bude najmanje :min.',
        'file' => 'Polje :attribute mora da bude najmanje :min kilobajta.',
        'string' => 'Polje :attribute mora da sadrži najmanje :min karaktera.',
        'array' => 'Polje :attribute mora da sadrži najmanje :min elemenata.',
    ],
    'multiple_of' => 'Polje :attribute mora da bude višestruko od :value.',
    'not_in' => 'Izabrano polje :attribute je neispravno.',
    'not_regex' => 'Format polja :attribute  je neispravan.',
    'numeric' => 'Polje :attribute mora da bude broj.',
    'password' => 'Lozinka je netačna.',
    'present' => 'Polje :attribute mora da bude prisutno.',
    'prohibited' => 'Polje :attribute je zabranjeno.',
    'prohibited_if' => 'Polje :attribute je zabranjeno kada je vrednost polja :other :value.',
    'prohibited_unless' => 'Polje :attribute je zabranjeno osim ako je vrednost polja :other :values.',
    'prohibits' => 'Polje :attribute zabranjuje polju :other da bude prisutno.',
    'regex' => 'Format polja :attribute je neispravan.',
    'required' => 'Polje :attribute je obavezno.',
    'required_array_keys' => 'Polje :attribute mora da sadrži unose za: :values.',
    'required_if' => 'Polje :attribute je obavezno kada je vrednost polja :other :value.',
    'required_unless' => 'Polje :attribute je obavezno osim ako je vrednost polja :other :values.',
    'required_with' => 'Polje :attribute je obavezno kada je polje :values prisutno.',
    'required_with_all' => 'Polje :attribute je obavezno kada su polja :values prisutna.',
    'required_without' => 'Polje :attribute je obavezno kada polja :values nisu prisutna.',
    'required_without_all' => 'Polje :attribute je obavezno kada nijedno od polja :values nije prisutno.',
    'same' => 'Polja :attribute i :other se ne poklapaju.',
    'size' => [
        'numeric' => 'Polje :attribute mora da bude :size.',
        'file' => 'Polje :attribute mora da bude :size kilobajta.',
        'string' => 'Polje :attribute mora da sadrži :size karaktera.',
        'array' => 'Polje :attribute mora da sadrži :size elementa.',
    ],
    'starts_with' => 'Polje :attribute mora da počne sa jednim od sledećih vrednosti: :values.',
    'string' => 'Polje :attribute mora da bude tekst.',
    'timezone' => 'Polje :attribute mora da bude ispravna vremenska zona.',
    'unique' => 'Polje :attribute je već zauzeto.',
    'uploaded' => 'Polje :attribute nije otpremljeno.',
    'url' => 'Polje :attribute mora da bude ispravan URL.',
    'uuid' => 'Polje :attribute mora da bude ispravan UUID.',


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
        'password_confirmation' => [
            'confirmed' => 'custom-message',
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

    'attributes' => [
        'first_name' => 'Ime',
        'last_name' => 'Prezime',
        'email' => 'Email',
        'password' => 'Šifra',
        'password_confirmation' => 'Potrvrda šifre',
        'question' => 'Pitanje',
        'name' => 'Naziv',
        'company_name' => 'Naziv kompanije',
        'phone_number' => 'Broj telefona',
        'position' => 'Pozicija'
        //'regulation_type_id' => 'Vrsta propisa'
    ],

];

