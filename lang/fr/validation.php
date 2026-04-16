<?php

return [
    'required'             => 'Le champ :attribute est obligatoire.',
    'string'               => 'Le champ :attribute doit être une chaîne de caractères.',
    'max'                  => [
        'string'  => 'Le champ :attribute ne peut pas dépasser :max caractères.',
        'numeric' => 'Le champ :attribute ne peut pas dépasser :max.',
        'file'    => 'Le fichier :attribute ne peut pas dépasser :max kilo-octets.',
    ],
    'min'                  => [
        'numeric' => 'Le champ :attribute doit être au moins :min.',
        'string'  => 'Le champ :attribute doit contenir au moins :min caractères.',
    ],
    'numeric'              => 'Le champ :attribute doit être un nombre.',
    'integer'              => 'Le champ :attribute doit être un entier.',
    'email'                => 'Le champ :attribute doit être une adresse e-mail valide.',
    'unique'               => 'Cette valeur est déjà utilisée.',
    'confirmed'            => 'La confirmation du champ :attribute ne correspond pas.',
    'image'                => 'Le fichier :attribute doit être une image.',
    'mimes'                => 'Le fichier :attribute doit être de type : :values.',
    'nullable'             => '',
    'boolean'              => 'Le champ :attribute doit être vrai ou faux.',
    'in'                   => 'La valeur sélectionnée pour :attribute est invalide.',
    'gt'                   => [
        'numeric' => 'Le champ :attribute doit être supérieur à :value.',
    ],
    'gte'                  => [
        'numeric' => 'Le champ :attribute doit être supérieur ou égal à :value.',
    ],

    'attributes' => [
        'name'             => 'nom',
        'email'            => 'adresse e-mail',
        'password'         => 'mot de passe',
        'price'            => 'prix',
        'price_promo'      => 'prix promotionnel',
        'stock'            => 'stock',
        'description'      => 'description',
        'category_id'      => 'catégorie',
        'image'            => 'image',
        'slug'             => 'slug',
        'phone'            => 'téléphone',
        'address'          => 'adresse',
        'wilaya_id'        => 'wilaya',
        'commune_id'       => 'commune',
        'quantity'         => 'quantité',
    ],
];
