<?php
/*
Plugin Name: MasterStudy LMS – WordPress Course Plugin Extension
Plugin URI: #
Description: Integração entre plugin MasterStudy LMS e ferramenta BuilderAll
Version: 1.0.7
Author: Guilherme Pereira
Author URI: #
*/


function funACEB4C1577E98AA583197C5AE076C2F7_get_key( $key ): ?string
{
    switch ( $key ) {
        case 'Primeiro nome':
            return 'FNAME';
        case 'Sobrenome':
            return 'SOBRENOME';
        case 'Telefone ( Whatsapp)':
            return 'PHONE';
        case 'Endereço':
            return 'ENDERECO';
        case 'Bairro':
            return 'BAIRRO';
        case 'Cidade':
            return 'CIDADE';
        case 'Estado':
            return 'ESTADO';
        case 'CEP':
            return 'CEP';
        default:
            return null;
    }
}

function funACEB4C1577E98AA583197C5AE076C2F7_lms_register_user( $user, $data ) {
    $logger = wc_get_logger();
    try {
        $user_info = get_userdata( $user );
        if( !is_bool( $user_info) ) {
            $url = "https://member.mailingboss.com/integration/index.php/lists/subscribers/create/721087:a431fdac4485bce002775c44dc02d2e2";
            $args = [
                'list_uid'      => '6253d3b972d0e',
                'email'         => $user_info->user_email,
            ];
            foreach ($data['profile_default_fields_for_register'] as $item) {
                $key = funACEB4C1577E98AA583197C5AE076C2F7_get_key($item['label']);
                if (!is_null($key)) {
                    $args[$key] = $item['value'];
                }           
            }
            foreach ($data['additional'] as $item) {
                $key = funACEB4C1577E98AA583197C5AE076C2F7_get_key($item['label']);
                if (!is_null($key)) {
                    $args[$key] = $item['value'];
                }
            }

            $response = wp_remote_post($url, [
                'body' => $args
            ]);
            if(is_wp_error($response)) {
                $logger->info($response->get_error_message(), [ 'source' => '[ LMS Extension try ]' ]);
            } else {
                $logger->info(implode(' ', $response), ['source' => '[ LMS Extension try ]']);
            }
        } 
    } catch (Exception $e) {
            $logger->error($e->getMessage(), [ 'source' => '[ LMS Extension catch ]' ]);
    }
 
}

add_action( 'init', function () {
    add_action( 'stm_lms_after_user_register', 'funACEB4C1577E98AA583197C5AE076C2F7_lms_register_user', 10, 2 );
} );