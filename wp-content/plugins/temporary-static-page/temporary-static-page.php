<?php
/**
 * Plugin Name: Pagina Temporanea Statica
 * Plugin URI: https://meuro.dev
 * Description: Plugin per mostrare una pagina statica temporanea personalizzabile al posto della homepage
 * Version: 1.0.0
 * Author: Meuro
 * License: GPL v2 or later
 * Text Domain: temporary-static-page
 */

// Prevenire accesso diretto
if (!defined('ABSPATH')) {
    exit;
}

// Definire costanti del plugin
define('TSP_PLUGIN_URL', plugin_dir_url(__FILE__));
define('TSP_PLUGIN_PATH', plugin_dir_path(__FILE__));

class TemporaryStaticPage {
    
    const OPTION_NAME = 'tsp_enabled';
    
    public function __construct() {
        add_action('init', array($this, 'init'));
        add_action('template_redirect', array($this, 'maybe_show_temporary_page'));
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'handle_admin_actions'));
        
        // Hook per attivazione/disattivazione plugin
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
    }
    
    public function init() {
        // Inizializzazione del plugin
    }
    
    public function activate() {
        // Non attiviamo automaticamente la modalità temporanea
        // L'admin dovrà farlo manualmente
    }
    
    public function deactivate() {
        // Disattiviamo la modalità temporanea quando il plugin viene disattivato
        delete_option(self::OPTION_NAME);
    }
    
    public function maybe_show_temporary_page() {
        // Controlla se dobbiamo mostrare la pagina temporanea
        if (!$this->should_show_temporary_page()) {
            return;
        }
        
        // Percorso del file temporaneo
        $temp_file = TSP_PLUGIN_PATH . 'temporary-page.php';
        
        // Se il file esiste, lo mostriamo
        if (file_exists($temp_file)) {
            // Impostiamo l'header appropriato
            status_header(200);
            header('Content-Type: text/html; charset=utf-8');
            
            // Includiamo il file e terminiamo l'esecuzione
            include $temp_file;
            exit;
        }
    }
    
    private function should_show_temporary_page() {
        // Non mostrare se la modalità temporanea non è attiva
        if (!get_option(self::OPTION_NAME, false)) {
            return false;
        }
        
        // Non mostrare agli amministratori
        if (current_user_can('manage_options')) {
            return false;
        }
        
        // Mostra solo sulla homepage
        return is_front_page() || is_home();
    }
    
    public function add_admin_menu() {
        add_options_page(
            'Pagina Temporanea',
            'Pagina Temporanea',
            'manage_options',
            'temporary-static-page',
            array($this, 'admin_page')
        );
    }
    
    public function handle_admin_actions() {
        if (!isset($_POST['tsp_nonce']) || !wp_verify_nonce($_POST['tsp_nonce'], 'tsp_action')) {
            return;
        }
        
        if (!current_user_can('manage_options')) {
            return;
        }
        
        if (isset($_POST['tsp_enable'])) {
            update_option(self::OPTION_NAME, true);
            add_action('admin_notices', function() {
                echo '<div class="notice notice-success"><p>Pagina temporanea <strong>attivata</strong>!</p></div>';
            });
        } elseif (isset($_POST['tsp_disable'])) {
            update_option(self::OPTION_NAME, false);
            add_action('admin_notices', function() {
                echo '<div class="notice notice-success"><p>Pagina temporanea <strong>disattivata</strong>!</p></div>';
            });
        }
    }
    
    public function admin_page() {
        $is_enabled = get_option(self::OPTION_NAME, false);
        $temp_file = TSP_PLUGIN_PATH . 'temporary-page.html';
        ?>
        <div class="wrap">
            <h1>Pagina Temporanea Statica</h1>
            
            <div class="card">
                <h2>Stato Attuale</h2>
                <p>
                    La modalità pagina temporanea è: 
                    <strong><?php echo $is_enabled ? 'ATTIVA' : 'DISATTIVA'; ?></strong>
                </p>
                
                <?php if ($is_enabled): ?>
                    <div class="notice notice-warning inline">
                        <p><strong>Attenzione:</strong> I visitatori vedranno la pagina temporanea invece della homepage normale. Gli amministratori continueranno a vedere il sito normale.</p>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="card">
                <h2>Controlli</h2>
                <form method="post" style="display: inline;">
                    <?php wp_nonce_field('tsp_action', 'tsp_nonce'); ?>
                    <?php if ($is_enabled): ?>
                        <button type="submit" name="tsp_disable" class="button button-secondary">
                            Disattiva Pagina Temporanea
                        </button>
                    <?php else: ?>
                        <button type="submit" name="tsp_enable" class="button button-primary">
                            Attiva Pagina Temporanea
                        </button>
                    <?php endif; ?>
                </form>
            </div>
            
            <div class="card">
                <h2>File Personalizzabile</h2>
                <p>
                    Il file della pagina temporanea si trova in:<br>
                    <code><?php echo esc_html($temp_file); ?></code>
                </p>
                
                <?php if (file_exists($temp_file)): ?>
                    <p style="color: green;">✓ Il file esiste ed è pronto per l'uso</p>
                    <?php if ($is_enabled): ?>
                        <p>
                            <a href="<?php echo home_url(); ?>" target="_blank" class="button">
                                Anteprima Pagina Temporanea
                            </a>
                            <small>(Apri in navigazione privata per vedere la pagina come visitatore)</small>
                        </p>
                    <?php endif; ?>
                <?php else: ?>
                    <p style="color: red;">⚠ Il file non esiste ancora</p>
                <?php endif; ?>
                
                <p>
                    <strong>Istruzioni:</strong><br>
                    Puoi modificare completamente il file <code>temporary-page.html</code> 
                    nella cartella del plugin per personalizzare la tua pagina temporanea.
                </p>
            </div>
        </div>
        <?php
    }
}

// Inizializza il plugin
new TemporaryStaticPage(); 