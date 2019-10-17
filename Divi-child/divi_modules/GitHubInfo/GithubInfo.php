<?php
class ET_Builder_Module_GithubInfo extends ET_Builder_Module {
    function init() {
        $this->name       = esc_html__( 'GitHub Info', 'et_builder' );
        $this->plural     = esc_html__( 'GitHub Info', 'et_builder' );
        $this->slug       = 'et_pb_github_info';
        $this->vb_support = 'off';
        $this->main_css_element = '%%order_class%% .et_pb_gh_info';

        $this->settings_modal_toggles = array(
            'general'  => array(
                'toggles' => array(
                    'main_content' => esc_html__( 'Content', 'et_builder' ),
                ),
            ),
        );
        wp_enqueue_style('gh_info', get_stylesheet_directory_uri() . '/divi_modules/GitHubInfo/css/gh_info-style.css');
    }

    function get_fields() {
        $fields = array(
            'gh_acc' => array(
                'label'             => esc_html__( 'GH User', 'et_builder' ),
                'type'              => 'text',
                'option_category'   => 'configuration',
                'description'       => esc_html__( 'Enter github account name', 'et_builder' ),
                'toggle_slug'       => 'main_content',
            ),
        );
        return $fields;
    }

    function gh_api_curl_connect($api_url) {
        $request = "GET";
        $curl = curl_init();
        $headers = array(
            'user-agent: test',
        );

        curl_setopt_array($curl, array(
            CURLOPT_URL             =>  $api_url,
            CURLOPT_CUSTOMREQUEST   =>  $request,
            CURLOPT_HTTPHEADER      =>  $headers,
            CURLOPT_RETURNTRANSFER  =>  true,
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    function get_gh_acc_info($user) {
        $api_url = 'https://api.github.com/users/' . $user;
        $result = $this->gh_api_curl_connect($api_url);
        $result = json_decode($result);
        $info = array(
            'name' => $result->login,
            'ava' => $result->avatar_url,
            'url' =>  $result->html_url,
            'create date' => $result->created_at,
            'repos_url' => $result->repos_url,
        );
        return $info;

    }

     function get_gh_repos($user) {
        $api_url = 'https://api.github.com/users/'. $user .'/repos';
        $result = $this->gh_api_curl_connect($api_url);
        $result = json_decode($result);
        return $result;
    }

    function get_gh_repo_info() {
        $repos = $this->get_gh_repos($this->props['gh_acc']);
        $names_output = array();
        foreach ($repos as $repo) {
            $names_output1 = sprintf(
              '<div class="rep_info">
                        <a href="%2$s">%1$s</a>
                        <div>%3$s</div>
                    </div>',
              $repo->name,
              $repo->html_url,
              $repo->language
            );
            array_push($names_output, $names_output1);
        }
        return implode('',$names_output);
    }

    function render( $attrs, $content = null, $render_slug ) {
        $repo_info = $this->get_gh_repo_info();
        $gh_acc_info = $this->get_gh_acc_info($this->props['gh_acc']);

        $gh_output = sprintf(
                    '<div class="gh_info_container">
                            <div class="content_container">
                                <h2>%1$s</h2>
                                <a href="%3$s"><img src="%2$s" alt="%2$s"></a>
                            </div>
                            <div class="repos_inf_container"><a href="%4$s"><h2>Repos</h2></a>%5$s</div>
                        </div>',
                    $gh_acc_info['name'],
                    $gh_acc_info['ava'],
                    $gh_acc_info['url'],
                    $gh_acc_info['repos_url'],
                    $repo_info
                );

        return $gh_output;
    }
}

new ET_Builder_Module_GithubInfo;