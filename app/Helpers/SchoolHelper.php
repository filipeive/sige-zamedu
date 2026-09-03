<?php

/**
 * Verifica se a escola registada é pública.
 */
if (!function_exists('is_public_school')) {
    function is_public_school(): bool
    {
        return setting('institution_type', 'escola_privada') === 'escola_publica';
    }
}

/**
 * Verifica se a escola registada é privada.
 */
if (!function_exists('is_private_school')) {
    function is_private_school(): bool
    {
        return !is_public_school();
    }
}
