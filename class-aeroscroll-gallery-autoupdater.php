<?php
#[AllowDynamicProperties]
class AeroscrollGalleryAutoUpdater
{
    public $plugin_slug;
    public $version;
    public $cache_key;
    public $cache_allowed;

    public function __construct()
    {
        $this->plugin_slug = plugin_basename(__DIR__);
        $this->version = AEROSCROLL_GALLERY_VERSION;
        $this->cache_key = 'aeroscroll_custom_upd';
        $this->cache_allowed = false;

        
        add_filter('pre_set_site_transient_update_plugins', array($this, 'update'));
        add_filter('plugins_api', array($this, 'info'), 20, 3);
        add_action('upgrader_process_complete', array($this, 'purge'), 10, 2);
    }

    public function request()
    {
        try {
            $remote = get_transient($this->cache_key);

            if (false === $remote || !$this->cache_allowed) {              

                $remote = wp_remote_get(
                    'https://www.aeroscroll.com/aeroscroll-updater/info.php',
                    array(
                        'timeout' => 10,
                        'headers' => array(
                            'Accept' => 'application/json' 
                        )
                    )
                );

                if (
                    is_wp_error($remote)
                    || 200 !== wp_remote_retrieve_response_code($remote)
                    || empty(wp_remote_retrieve_body($remote))
                ) {
                    return false;
                }

                set_transient($this->cache_key, $remote, PLUGIN_CHECK_TRANSIENT_EXPIRATION);
            }

            $remote = json_decode(wp_remote_retrieve_body($remote));            
        }
        catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }

        return $remote;
    }


    function info($res, $action, $args)
    {
        // do nothing if you're not getting plugin information right now
        if ('plugin_information' !== $action) {
            return $res;
        }

        // do nothing if it is not our plugin
        if ($this->plugin_slug !== $args->slug) {
            return $res;
        }

        // get updates
        $remote = $this->request();

        if (!$remote) {
            return $res;
        }

        $res = new stdClass();

        $res->name = $remote->name;
        $res->slug = $remote->slug;
        $res->version = $remote->version;
        $res->tested = $remote->tested;
        $res->requires = $remote->requires;
        $res->author = $remote->author;
        $res->author_profile = $remote->author_profile;
        $res->download_link = $remote->download_url;
        $res->trunk = $remote->download_url;
        $res->requires_php = $remote->requires_php;
        $res->last_updated = $remote->last_updated;

        $res->sections = array(
            'description' => $remote->sections->description,
            'installation' => $remote->sections->installation,
            'changelog' => $remote->sections->changelog
        );

        if (!empty($remote->banners)) {
            $res->banners = array(
                'low' => $remote->banners->low,
                'high' => $remote->banners->high
            );
        }

        return $res;
    }

    public function update($transient)
    {
        if (empty($transient->checked)) {
            return $transient;
        }

        $remote = $this->request();

        if (
            $remote
            && version_compare($this->version, $remote->version, '<')
            && version_compare($remote->requires, get_bloginfo('version'), '<=')
            && version_compare($remote->requires_php, PHP_VERSION, '<')
        ) {
            $res = new stdClass();
            $res->slug = $this->plugin_slug;
            $res->plugin = "aeroscroll-gallery/aeroscroll-gallery.php";
            $res->new_version = $remote->version;
            $res->tested = $remote->tested;
            $res->package = $remote->download_url;
            $res->trunk = $remote->download_url;
            $res->tested = $remote->tested;
            $res->requires = $remote->requires;
            $res->requires_php = $remote->requires_php;
            $res->last_updated = $remote->last_updated;            
            $res->sections = array(
                'description' => $remote->sections->description,
                'installation' => $remote->sections->installation,
                'changelog' => $remote->sections->changelog
            );
            
            $transient->response[$res->plugin] = $res;
        }

        return $transient;
    }

    public function purge($upgrader, $options)
    {

        if (
            $this->cache_allowed
            && 'update' === $options['action']
            && 'plugin' === $options['type']
        ) {
            // just clean the cache when new plugin version is installed
            delete_transient($this->cache_key);
        }
    }

    function aeroscroll_update_message( $plugin_data, $new_data ) {
        if ( isset( $plugin_data['update'] ) && $plugin_data['update'] && isset( $new_data->upgrade_notice ) ) {
            printf(
                '<div class="update-message"><p><strong>%s</strong>: %s</p></div>',
                $new_data -> new_version,
                wpautop( $new_data -> upgrade_notice )
            );
        }
    }
}
