<?php
/**
 * Plugin Name: Custom Plugin - Dubai Business Setup Forms
 * Description: A comprehensive lead-capture plugin for Dubai business setup services. Features three form types: Business Setup Cost Calculator, Expert Support Form, and Pricing Grid with popup. Use shortcode [custom_plugin_calculator] with form_type parameter (calculator, expert_support, pricing_grid).
 * Version: 1.0.0
 * Author: Muhammad Hassan
 * 
 * Shortcodes:
 * [custom_plugin_calculator] - Default calculator form
 * [custom_plugin_calculator form_type="calculator"] - Business setup cost calculator
 * [custom_plugin_calculator form_type="expert_support"] - Expert support contact form
 * [custom_plugin_calculator form_type="pricing_grid"] - Pricing packages grid with consultation popup
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Custom_Plugin_Calculator {

    public function __construct() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_assets'));
        add_shortcode('custom_plugin_calculator', array($this, 'render_shortcode'));
        
        add_action('wp_ajax_submit_custom_calculator', array($this, 'handle_ajax_submission'));
        add_action('wp_ajax_nopriv_submit_custom_calculator', array($this, 'handle_ajax_submission'));

        // Register custom element for Avada Builder
        add_action('fusion_builder_before_init', array($this, 'map_avada_element'));

        // Add Global Expert Support Modal to Footer
        add_action('wp_footer', array($this, 'render_expert_popup_footer'));
    }

    public function render_expert_popup_footer() {
        ?>
        <!-- Global Expert Support Modal -->
        <div class="modal-overlay" id="expertSupportModal">
            <div class="modal-container expert-modal-container" style="max-width: 600px;">
                <button class="modal-close" id="closeExpertModal" aria-label="Close Contact Modal">&times;</button>
                
                <div class="expert-support-container" style="box-shadow: none; margin: 0; width: 100%; max-width: 100%; padding: 40px;">
                    <form class="expert-support-form js-expert-support-form">
                        <h2>Get an expert support</h2>
                        <p class="expert-support-subtitle">Fill in your contact details, and we'll get back to you soon.</p>

                        <div class="expert-form-body">
                            <div class="expert-toggle-container">
                                <input type="hidden" name="expert_contact_type" class="expert_contact_type" value="phone">
                                <button type="button" class="expert-toggle-btn active" data-type="phone" aria-label="Select Phone Contact">
                                    <svg viewBox="0 0 24 24" width="16" height="16" class="expert-icon" aria-hidden="true"><path fill="currentColor" d="M20.01 15.38c-1.23 0-2.42-.2-3.53-.56-.35-.12-.74-.03-1.01.24l-1.57 1.97c-2.83-1.35-5.48-3.9-6.89-6.83l1.95-1.66c.27-.28.35-.67.24-1.02-.37-1.11-.56-2.3-.56-3.53 0-.54-.45-.99-.99-.99H4.19C3.65 3 3 3.24 3 3.99 3 13.28 10.73 21 20.03 21c.76 0 .98-.66.98-1.21v-3.42c0-.54-.45-.99-.99-.99z"/></svg>
                                    PHONE
                                </button>
                                <button type="button" class="expert-toggle-btn" data-type="email" aria-label="Select Email Contact">
                                    <svg viewBox="0 0 24 24" width="16" height="16" class="expert-icon" aria-hidden="true"><path fill="currentColor" d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>
                                    EMAIL
                                </button>
                            </div>

                            <div class="input-line">
                                <div class="country-code-container expert_phone_container">
                                    <span class="flag-icon" aria-hidden="true">🇦🇪</span> <span class="flag-text">+971</span>
                                    <input type="tel" name="contact_phone" class="expert_phone" placeholder="Phone number" aria-label="Phone number" required>
                                </div>
                                <div class="email-container expert_email_container" style="display:none;">
                                    <input type="email" name="contact_email" class="expert_email" placeholder="Email Address" aria-label="Email Address">
                                </div>
                            </div>

                            <div class="input-submit-line">
                                <input type="text" name="contact_name" class="expert-name-input" placeholder="your name" aria-label="Your Name" required>
                                <button type="submit" class="expert-submit-btn js-expert-submit-btn">SUBMIT</button>
                            </div>

                            <div class="expert-divider">
                                <span aria-hidden="true">or</span>
                            </div>

                            <div class="expert-whatsapp">
                                <a href="https://wa.me/971000000000" target="_blank" class="whatsapp-link" aria-label="Contact us on WhatsApp">
                                    <svg viewBox="0 0 24 24" width="24" height="24" style="color: #25D366; margin-right: 8px;" aria-hidden="true"><path fill="currentColor" d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 0 0-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z"/>
                                    </svg>
                                    <u>Contact us on WhatsApp</u>
                                </a>
                            </div>
                        </div>
                        <div class="expert_msg" style="display:none; margin-top:20px; font-weight:bold; text-align:center;" role="alert"></div>
                        <input type="hidden" name="form_source" value="expert_support">
                    </form>
                </div>
            </div>
        </div>
        <?php
    }

    public function map_avada_element() {
        if (function_exists('fusion_builder_map')) {
            fusion_builder_map(array(
                'name'            => esc_attr__('Custom Forms', 'fusion-builder'),
                'shortcode'       => 'custom_plugin_calculator',
                'icon'            => 'fusiona-calculator',
                'description'     => esc_attr__('Inserts the custom forms (Calculator or Expert Support).', 'fusion-builder'),
                'allow_generator' => true,
                'params'          => array(
                    array(
                        'type'        => 'select',
                        'heading'     => esc_attr__('Select Form Type', 'fusion-builder'),
                        'description' => esc_attr__('Choose which form to display.', 'fusion-builder'),
                        'param_name'  => 'form_type',
                        'value'       => array(
                            'calculator'      => esc_attr__('Business Setup Cost Calculator', 'fusion-builder'),
                            'expert_support'  => esc_attr__('Expert Support Form', 'fusion-builder'),
                            'pricing_grid'    => esc_attr__('Pricing Grid with Popup', 'fusion-builder'),
                            'visa_calculator' => esc_attr__('UAE Visa Cost Calculator', 'fusion-builder'),
                        ),
                        'default'     => 'calculator',
                    )
                )
            ));
        }
    }

    public function enqueue_assets() {
        $css_ver = file_exists(plugin_dir_path(__FILE__) . 'assets/style.css') ? filemtime(plugin_dir_path(__FILE__) . 'assets/style.css') : '1.0.0';
        $js_ver  = file_exists(plugin_dir_path(__FILE__) . 'assets/script.js') ? filemtime(plugin_dir_path(__FILE__) . 'assets/script.js') : '1.0.0';

        wp_enqueue_style('custom-plugin-css', plugin_dir_url(__FILE__) . 'assets/style.css', array(), $css_ver);
        wp_enqueue_script('custom-plugin-js', plugin_dir_url(__FILE__) . 'assets/script.js', array(), $js_ver, true);
        
        wp_localize_script('custom-plugin-js', 'customPluginObj', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('custom_calculator_nonce')
        ));
    }

    public function render_shortcode($atts) {
        $atts = shortcode_atts(array(
            'form_type' => 'calculator'
        ), $atts, 'custom_plugin_calculator');

        ob_start();

        if ($atts['form_type'] === 'expert_support') {
            ?>
            <div class="expert-support-container">
                <form class="expert-support-form js-expert-support-form">
                    <h2>Get an expert support</h2>
                    <p class="expert-support-subtitle">Fill in your contact details, and we'll get back to you soon.</p>

                    <div class="expert-form-body">
                        <div class="expert-toggle-container">
                            <input type="hidden" name="expert_contact_type" class="expert_contact_type" value="phone">
                            <button type="button" class="expert-toggle-btn active" data-type="phone" aria-label="Select Phone Contact">
                                <svg viewBox="0 0 24 24" width="16" height="16" class="expert-icon" aria-hidden="true"><path fill="currentColor" d="M20.01 15.38c-1.23 0-2.42-.2-3.53-.56-.35-.12-.74-.03-1.01.24l-1.57 1.97c-2.83-1.35-5.48-3.9-6.89-6.83l1.95-1.66c.27-.28.35-.67.24-1.02-.37-1.11-.56-2.3-.56-3.53 0-.54-.45-.99-.99-.99H4.19C3.65 3 3 3.24 3 3.99 3 13.28 10.73 21 20.03 21c.76 0 .98-.66.98-1.21v-3.42c0-.54-.45-.99-.99-.99z"/></svg>
                                PHONE
                            </button>
                            <button type="button" class="expert-toggle-btn" data-type="email" aria-label="Select Email Contact">
                                <svg viewBox="0 0 24 24" width="16" height="16" class="expert-icon" aria-hidden="true"><path fill="currentColor" d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>
                                EMAIL
                            </button>
                        </div>

                        <div class="input-line">
                            <div class="country-code-container expert_phone_container">
                                <span class="flag-icon" aria-hidden="true">🇦🇪</span> <span class="flag-text">+971</span>
                                <input type="tel" name="contact_phone" class="expert_phone" placeholder="Phone number" aria-label="Phone number" required>
                            </div>
                            <div class="email-container expert_email_container" style="display:none;">
                                <input type="email" name="contact_email" class="expert_email" placeholder="Email Address" aria-label="Email Address">
                            </div>
                        </div>

                        <div class="input-submit-line">
                            <input type="text" name="contact_name" class="expert-name-input" placeholder="your name" aria-label="Your Name" required>
                            <button type="submit" class="expert-submit-btn js-expert-submit-btn">SUBMIT</button>
                        </div>

                        <div class="expert-divider">
                            <span aria-hidden="true">or</span>
                        </div>

                        <div class="expert-whatsapp">
                            <a href="https://wa.me/971000000000" target="_blank" class="whatsapp-link" aria-label="Contact us on WhatsApp">
                                <svg viewBox="0 0 24 24" width="24" height="24" style="color: #25D366; margin-right: 8px;" aria-hidden="true"><path fill="currentColor" d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 0 0-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z"/></svg>
                                <u>Contact us on WhatsApp</u>
                            </a>
                        </div>
                    </div>
                    <div class="expert_msg" style="display:none; margin-top:20px; font-weight:bold; text-align:center;" role="alert"></div>
                    <input type="hidden" name="form_source" value="expert_support">
                </form>
            </div>
            <?php
        } elseif ($atts['form_type'] === 'pricing_grid') {
            ?>
            <div class="section-container">
                <h1 class="header-title">What does it cost to establish a company in the UAE?</h1>
                <p class="header-teaser">
                    Prices depend on the registering authority, license type, types and number of business activities and the number of visas required.
                </p>

                <div class="pricing-grid">
                    <div class="pricing-card" data-package="UAE Free Zones" data-price="5,750 AED">
                        <div class="card-caption">UAE Free Zones</div>
                        <span class="license-label">license from</span>
                        <div class="price">5,750 AED</div>
                        <div class="best-choice-title">Best choice for</div>
                        <ul class="feature-list">
                            <li class="feature-item">UAE residency</li>
                            <li class="feature-item">Business operations within and beyond UAE</li>
                        </ul>
                        <button class="btn-discuss trigger-popup">Discuss Details</button>
                        <a href="#" class="link-more">Learn more</a>
                    </div>

                    <div class="pricing-card" data-package="Dubai Offshore Company" data-price="9,500 AED">
                        <div class="card-caption">Dubai Offshore Company</div>
                        <span class="license-label">license from</span>
                        <div class="price">9,500 AED</div>
                        <div class="best-choice-title">Best choice for</div>
                        <ul class="feature-list">
                            <li class="feature-item">Holding company</li>
                            <li class="feature-item">International business operations</li>
                        </ul>
                        <button class="btn-discuss trigger-popup">Discuss Details</button>
                        <a href="#" class="link-more">Learn more</a>
                    </div>

                    <div class="pricing-card" data-package="Dubai Free Zones" data-price="12,750 AED">
                        <div class="card-caption">Dubai Free Zones</div>
                        <span class="license-label">license from</span>
                        <div class="price">12,750 AED</div>
                        <div class="best-choice-title">Best choice for</div>
                        <ul class="feature-list">
                            <li class="feature-item">UAE residency</li>
                            <li class="feature-item">Business operations within and beyond UAE</li>
                        </ul>
                        <button class="btn-discuss trigger-popup">Discuss Details</button>
                        <a href="#" class="link-more">Learn more</a>
                    </div>

                    <div class="pricing-card" data-package="Dubai Mainland" data-price="16,500 AED">
                        <div class="card-caption">Dubai Mainland</div>
                        <span class="license-label">license from</span>
                        <div class="price">16,500 AED</div>
                        <div class="best-choice-title">Best choice for</div>
                        <ul class="feature-list">
                            <li class="feature-item">Doing business in UAE local market</li>
                            <li class="feature-item">Opening retail business</li>
                            <li class="feature-item">Unlimited visas</li>
                        </ul>
                        <button class="btn-discuss trigger-popup">Discuss Details</button>
                        <a href="#" class="link-more">Learn more</a>
                    </div>
                </div>
            </div>

            <!-- Modal Structure -->
            <div class="modal-overlay" id="consultationModal">
                <div class="modal-container">
                    <button class="modal-close" id="closeModal" aria-label="Close Contact Modal">&times;</button>
                    
                    <div class="modal-left">
                        <div class="expert-photo">
                        </div>
                    </div>

                    <div class="modal-right">
                        <h2 class="modal-title">Get a free consultation</h2>
                        <p class="modal-subtitle">Fill out the form to contact an expert</p>

                        <form id="consultationForm" class="js-pricing-popup-form">
                            <!-- Hidden fields to store user's selected package -->
                            <input type="hidden" id="selectedPackageName" name="selectedPackageName" value="">
                            <input type="hidden" id="selectedPackagePrice" name="selectedPackagePrice" value="">
                            
                            <!-- Display selected package info -->
                            <div class="selected-package-info" id="packageInfoDisplay" style="display: none;">
                                Interested in: <strong id="displayTargetPackage"></strong> (<span id="displayTargetPrice"></span>)
                            </div>

                            <div class="contact-tabs">
                                <input type="hidden" name="contact_type" class="pricing_contact_type" value="phone">
                                <button type="button" class="tab-btn active" data-target="phone">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                                    PHONE
                                </button>
                                <button type="button" class="tab-btn" data-target="email">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                                    EMAIL
                                </button>
                            </div>

                            <div id="phoneInputArea">
                                <div class="phone-input-wrapper">
                                    <div class="country-code">
                                        +971
                                    </div>
                                    <input type="tel" class="phone-input pricing_phone" placeholder="50 123 4567" name="contact_phone" required>
                                </div>
                            </div>

                            <div id="emailInputArea" style="display: none;">
                                <div class="form-row">
                                    <input type="email" class="form-input pricing_email" placeholder="your@email.com" name="contact_email">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="input-group">
                                    <input type="text" class="form-input pricing_name" placeholder="your name" name="contact_name" required>
                                </div>
                                <button type="submit" class="submit-btn pricing-submit-btn js-pricing-submit">
                                    SUBMIT
                                </button>
                            </div>

                            <div class="pricing_msg" style="display:none; margin-top:20px; font-weight:bold; text-align:center;" role="alert"></div>
                            <input type="hidden" name="form_source" value="pricing_grid">
                        </form>

                        <div class="divider"><span>or</span></div>

                        <a href="https://wa.me/971000000000" target="_blank" class="whatsapp-link">
                            <svg class="whatsapp-icon" viewBox="0 0 24 24" width="24" height="24" fill="currentColor">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 0 0-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z"/>
                            </svg>
                            Contact us on WhatsApp
                        </a>
                    </div>
                </div>
            </div>
            <?php
        } elseif ($atts['form_type'] === 'visa_calculator') {
            ?>
            <div class="dubai-calc-container">
                <form class="dubai-calc-form js-dubai-calc-form">
                    <div class="calc-section">
                        <label class="section-label">Do you need visas for family members? <span style="color:#aaa;font-weight:normal;">(optional)</span></label>
                        
                        <div style="margin-top: 15px;">
                            <label style="display:block;margin-bottom:8px;font-weight:bold;font-size:14px;">Spouse(s)</label>
                            <div class="pill-group" data-group="spouse_visas">
                                <button type="button" class="pill-btn" data-value="Yes" aria-label="Yes">
                                    <svg class="check-icon" viewBox="0 0 24 24" width="16" height="16" aria-hidden="true"><path fill="currentColor" d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg> Yes
                                </button>
                                <button type="button" class="pill-btn" data-value="No" aria-label="No">
                                    <svg class="check-icon" viewBox="0 0 24 24" width="16" height="16" aria-hidden="true"><path fill="currentColor" d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg> No
                                </button>
                                <button type="button" class="pill-btn" data-value="Haven't decided yet" aria-label="Haven't decided yet">
                                    <svg class="check-icon" viewBox="0 0 24 24" width="16" height="16" aria-hidden="true"><path fill="currentColor" d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg> Haven't decided yet
                                </button>
                                <input type="hidden" name="spouse_visas" class="spouse_visas" value="">
                            </div>
                        </div>

                        <div style="margin-top: 15px;">
                            <label style="display:block;margin-bottom:8px;font-weight:bold;font-size:14px;">Children</label>
                            <div class="pill-group" data-group="children_visas">
                                <button type="button" class="pill-btn" data-value="Yes" aria-label="Yes">
                                    <svg class="check-icon" viewBox="0 0 24 24" width="16" height="16" aria-hidden="true"><path fill="currentColor" d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg> Yes
                                </button>
                                <button type="button" class="pill-btn" data-value="No" aria-label="No">
                                    <svg class="check-icon" viewBox="0 0 24 24" width="16" height="16" aria-hidden="true"><path fill="currentColor" d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg> No
                                </button>
                                <button type="button" class="pill-btn" data-value="Haven't decided yet" aria-label="Haven't decided yet">
                                    <svg class="check-icon" viewBox="0 0 24 24" width="16" height="16" aria-hidden="true"><path fill="currentColor" d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg> Haven't decided yet
                                </button>
                                <input type="hidden" name="children_visas" class="children_visas" value="">
                            </div>
                        </div>

                        <div style="margin-top: 15px;">
                            <label style="display:block;margin-bottom:8px;font-weight:bold;font-size:14px;">Parents</label>
                            <div class="pill-group" data-group="parents_visas">
                                <button type="button" class="pill-btn" data-value="Yes" aria-label="Yes">
                                    <svg class="check-icon" viewBox="0 0 24 24" width="16" height="16" aria-hidden="true"><path fill="currentColor" d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg> Yes
                                </button>
                                <button type="button" class="pill-btn" data-value="No" aria-label="No">
                                    <svg class="check-icon" viewBox="0 0 24 24" width="16" height="16" aria-hidden="true"><path fill="currentColor" d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg> No
                                </button>
                                <input type="hidden" name="parents_visas" class="parents_visas" value="">
                            </div>
                        </div>
                    </div>

                    <div class="calc-section">
                        <label class="section-label">Select the type of visa you want to apply for <span style="color:#aaa;font-weight:normal;">(optional)</span></label>
                        <div class="radio-group radio-vertical">
                            <label class="custom-checkbox">
                                <input type="checkbox" name="visa_type[]" value="10-year Golden visa for buying property">
                                <span class="checkbox-indicator"></span> 10-year Golden visa for buying property
                            </label>
                            <label class="custom-checkbox">
                                <input type="checkbox" name="visa_type[]" value="10-year Golden visa for a bank deposit">
                                <span class="checkbox-indicator"></span> 10-year Golden visa for a bank deposit
                            </label>
                            <label class="custom-checkbox">
                                <input type="checkbox" name="visa_type[]" value="10-year Golden visa for executives">
                                <span class="checkbox-indicator"></span> 10-year Golden visa for executives
                            </label>
                            <label class="custom-checkbox">
                                <input type="checkbox" name="visa_type[]" value="2-year visa for opening a company">
                                <span class="checkbox-indicator"></span> 2-year visa for opening a company
                            </label>
                        </div>
                    </div>

                    <div class="calc-section">
                        <label class="section-label">Additional comments</label>
                        <textarea name="comments" placeholder="Enter text" rows="3" class="custom-textarea" aria-label="Additional comments"></textarea>
                    </div>

                    <div class="calc-section contact-section">
                        <label class="section-label">Contact information <span style="color:#e6292f;">(required)</span></label>
                        <div class="contact-toggle">
                            <button type="button" class="toggle-btn active" data-type="email" aria-label="Select Email">
                                <svg viewBox="0 0 24 24" width="16" height="16" aria-hidden="true"><path fill="currentColor" d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>
                                EMAIL
                            </button>
                            <button type="button" class="toggle-btn" data-type="whatsapp" aria-label="Select WhatsApp">
                                <svg viewBox="0 0 24 24" width="16" height="16" aria-hidden="true"><path fill="currentColor" d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 0 0-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z"/></svg>
                                WHATSAPP
                            </button>
                        </div>
                        <input type="email" name="contact_value" class="contact-input js-contact-input" placeholder="Email" aria-label="Email or Phone Value" required>
                        <input type="hidden" name="contact_type" class="contact_type js-contact_type" value="email">
                    </div>

                    <button type="submit" class="submit-btn js-dubai-calc-submit">
                        GET A QUOTE IN 15 MINUTES*
                    </button>
                    <div class="form-footer">
                        *Applications are processed during working hours: Monday to Friday, 9:00 AM to 6:00 PM (Dubai time)
                    </div>
                    
                    <div class="dubai_calc_message" style="display:none; margin-top:20px; font-weight:bold; text-align:center;" role="alert"></div>
                    <input type="hidden" name="form_source" value="visa_calculator">
                </form>
            </div>
            <?php
        } else {
            ?>
            <div class="dubai-calc-container">
                <form class="dubai-calc-form js-dubai-calc-form">
                    <h2 style="text-align: left !important; clear: both; width: 100%;">Business Setup Cost Calculation in Dubai</h2>

                    <div class="calc-section">
                        <label class="section-label">Type of activity (optional)</label>
                        <div class="radio-group radio-vertical">
                            <label class="custom-radio">
                                <input type="radio" name="activity_type" value="Trade">
                                <span class="radio-indicator"></span> Trade
                            </label>
                            <label class="custom-radio">
                                <input type="radio" name="activity_type" value="Services">
                                <span class="radio-indicator"></span> Services
                            </label>
                            <label class="custom-radio">
                                <input type="radio" name="activity_type" value="Manufacturing">
                                <span class="radio-indicator"></span> Manufacturing
                            </label>
                            <label class="custom-radio">
                                <input type="radio" name="activity_type" value="Haven't decided yet">
                                <span class="radio-indicator"></span> Haven't decided yet
                            </label>
                        </div>
                    </div>

                    <div class="calc-section">
                        <label class="section-label">Additional comments</label>
                        <textarea name="comments" placeholder="Enter text" rows="3" class="custom-textarea" aria-label="Additional comments"></textarea>
                    </div>

                    <div class="calc-section">
                        <label class="section-label">Office requirements (optional)</label>
                        <div class="pill-group" data-group="office_req">
                            <button type="button" class="pill-btn" data-value="Office required" aria-label="Office required">
                                <svg class="check-icon" viewBox="0 0 24 24" width="16" height="16" aria-hidden="true"><path fill="currentColor" d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                                Office required
                            </button>
                            <button type="button" class="pill-btn" data-value="No office required" aria-label="No office required">
                                <svg class="check-icon" viewBox="0 0 24 24" width="16" height="16" aria-hidden="true"><path fill="currentColor" d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                                No office required
                            </button>
                            <button type="button" class="pill-btn" data-value="Haven't decided yet" aria-label="Haven't decided yet">
                                <svg class="check-icon" viewBox="0 0 24 24" width="16" height="16" aria-hidden="true"><path fill="currentColor" d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                                Haven't decided yet
                            </button>
                            <input type="hidden" name="office_req" class="office_req" value="">
                        </div>
                    </div>

                    <div class="calc-section">
                        <label class="section-label">Number of shareholders (optional)</label>
                        <div class="circle-pill-group" data-group="shareholders">
                            <button type="button" class="circle-btn" data-value="1" aria-label="1 shareholder">1</button>
                            <button type="button" class="circle-btn" data-value="2" aria-label="2 shareholders">2</button>
                            <button type="button" class="circle-btn" data-value="3" aria-label="3 shareholders">3</button>
                            <button type="button" class="circle-btn" data-value="4+" aria-label="4 or more shareholders">4+</button>
                            <button type="button" class="pill-btn" data-value="Haven't decided yet" aria-label="Haven't decided yet">Haven't decided yet</button>
                            <input type="hidden" name="shareholders" class="shareholders" value="">
                        </div>
                    </div>

                    <div class="calc-section">
                        <label class="section-label">Number of employees (optional)</label>
                        <div class="circle-pill-group" data-group="employees">
                            <button type="button" class="circle-btn" data-value="1" aria-label="1 employee">1</button>
                            <button type="button" class="circle-btn" data-value="2" aria-label="2 employees">2</button>
                            <button type="button" class="circle-btn" data-value="3" aria-label="3 employees">3</button>
                            <button type="button" class="circle-btn" data-value="4+" aria-label="4 or more employees">4+</button>
                            <button type="button" class="pill-btn" data-value="Haven't decided yet" aria-label="Haven't decided yet">Haven't decided yet</button>
                            <input type="hidden" name="employees" class="employees" value="">
                        </div>
                    </div>

                    <div class="calc-section contact-section">
                        <label class="section-label">Contact information (required)</label>
                        <div class="contact-toggle">
                            <button type="button" class="toggle-btn active" data-type="email" aria-label="Select Email">
                                <svg viewBox="0 0 24 24" width="16" height="16" aria-hidden="true"><path fill="currentColor" d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>
                                EMAIL
                            </button>
                            <button type="button" class="toggle-btn" data-type="whatsapp" aria-label="Select WhatsApp">
                                <svg viewBox="0 0 24 24" width="16" height="16" aria-hidden="true"><path fill="currentColor" d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 0 0-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z"/></svg>
                                WHATSAPP
                            </button>
                        </div>
                        <input type="email" name="contact_value" class="contact-input js-contact-input" placeholder="Email" aria-label="Email or Phone Value" required>
                        <input type="hidden" name="contact_type" class="contact_type js-contact_type" value="email">
                    </div>

                    <button type="submit" class="submit-btn js-dubai-calc-submit">
                        GET A QUOTE IN 15 MINUTES*
                    </button>
                    <div class="form-footer">
                        *Applications are processed during working hours: Monday to Friday, 9:00 AM to 6:00 PM (GST).
                    </div>
                    
                    <div class="dubai_calc_message" style="display:none; margin-top:20px; font-weight:bold; text-align:center;" role="alert"></div>
                    <input type="hidden" name="form_source" value="calculator">
                </form>
            </div>
            <?php
        }
        return ob_get_clean();
    }

    public function handle_ajax_submission() {
        check_ajax_referer('custom_calculator_nonce', 'nonce');

        $form_source = isset($_POST['form_source']) ? sanitize_text_field($_POST['form_source']) : 'calculator';
        $admin_email = get_option('admin_email');

        if ($form_source === 'expert_support') {
            $contact_type = isset($_POST['expert_contact_type']) ? sanitize_text_field($_POST['expert_contact_type']) : 'phone';
            $contact_name = isset($_POST['contact_name']) ? sanitize_text_field($_POST['contact_name']) : '';
            
            if ($contact_type === 'email') {
                $contact_value = isset($_POST['contact_email']) ? sanitize_email($_POST['contact_email']) : '';
            } else {
                $contact_value = isset($_POST['contact_phone']) ? sanitize_text_field($_POST['contact_phone']) : '';
            }

            if (empty($contact_value) || empty($contact_name)) {
                wp_send_json_error(array('message' => 'Please fill in required fields.'));
                return;
            }

            $subject = 'New Expert Support Request';
            $message = "You have received a new Expert Support request.\n\n";
            $message .= "Name: $contact_name\n";
            $message .= "Contact Method: " . ucfirst($contact_type) . "\n";
            $message .= "Contact Details: $contact_value\n";

            wp_mail($admin_email, $subject, $message);
            wp_send_json_success(array('message' => 'Thank you! We will get back to you soon.'));

        } elseif ($form_source === 'pricing_grid') {
            $contact_type = isset($_POST['contact_type']) ? sanitize_text_field($_POST['contact_type']) : 'phone';
            $contact_name = isset($_POST['contact_name']) ? sanitize_text_field($_POST['contact_name']) : '';
            $package_name = isset($_POST['selectedPackageName']) ? sanitize_text_field($_POST['selectedPackageName']) : '';
            $package_price = isset($_POST['selectedPackagePrice']) ? sanitize_text_field($_POST['selectedPackagePrice']) : '';
            
            if ($contact_type === 'email') {
                $contact_value = isset($_POST['contact_email']) ? sanitize_email($_POST['contact_email']) : '';
            } else {
                $contact_value = isset($_POST['contact_phone']) ? sanitize_text_field($_POST['contact_phone']) : '';
            }

            if (empty($contact_value) || empty($contact_name)) {
                wp_send_json_error(array('message' => 'Please fill in required fields.'));
                return;
            }

            $subject = 'New Pricing Inquiry';
            $message = "You have received a new inquiry from the Pricing Grid.\n\n";
            $message .= "Package: $package_name\n";
            $message .= "Price: $package_price\n";
            $message .= "Name: $contact_name\n";
            $message .= "Contact Method: " . ucfirst($contact_type) . "\n";
            $message .= "Contact Details: $contact_value\n";

            wp_mail($admin_email, $subject, $message);
            wp_send_json_success(array('message' => 'Thank you! We will get back to you soon.'));

        } elseif ($form_source === 'visa_calculator') {
            $spouse_visas   = isset($_POST['spouse_visas']) ? sanitize_text_field($_POST['spouse_visas']) : '';
            $children_visas = isset($_POST['children_visas']) ? sanitize_text_field($_POST['children_visas']) : '';
            $parents_visas  = isset($_POST['parents_visas']) ? sanitize_text_field($_POST['parents_visas']) : '';
            
            $visa_types = isset($_POST['visa_type']) && is_array($_POST['visa_type']) ? array_map('sanitize_text_field', $_POST['visa_type']) : array();
            $visa_types_str = !empty($visa_types) ? implode(', ', $visa_types) : 'None selected';
            
            $comments      = isset($_POST['comments']) ? sanitize_textarea_field($_POST['comments']) : '';
            $contact_type  = isset($_POST['contact_type']) ? sanitize_text_field($_POST['contact_type']) : '';
            
            if ($contact_type === 'email') {
                $contact_value = isset($_POST['contact_value']) ? sanitize_email($_POST['contact_value']) : '';
            } else {
                $contact_value = isset($_POST['contact_value']) ? sanitize_text_field($_POST['contact_value']) : '';
            }

            if (empty($contact_value)) {
                wp_send_json_error(array('message' => 'Contact information is required.'));
                return;
            }

            $subject     = 'New UAE Visa Cost Calculation Request';
            $message     = "You have received a new visa quote request.\n\n";
            $message    .= "Spouse(s) Visas: $spouse_visas\n";
            $message    .= "Children Visas: $children_visas\n";
            $message    .= "Parents Visas: $parents_visas\n";
            $message    .= "Visa Types Selected: $visa_types_str\n";
            $message    .= "Comments: $comments\n";
            $message    .= "Contact Type: $contact_type\n";
            $message    .= "Contact Value: $contact_value\n";

            wp_mail($admin_email, $subject, $message);
            wp_send_json_success(array('message' => 'Thank you! Your request has been received.'));

        } else {
            $activity_type = isset($_POST['activity_type']) ? sanitize_text_field($_POST['activity_type']) : '';
            $comments      = isset($_POST['comments']) ? sanitize_textarea_field($_POST['comments']) : '';
            $office_req    = isset($_POST['office_req']) ? sanitize_text_field($_POST['office_req']) : '';
            $shareholders  = isset($_POST['shareholders']) ? sanitize_text_field($_POST['shareholders']) : '';
            $employees     = isset($_POST['employees']) ? sanitize_text_field($_POST['employees']) : '';
            $contact_type  = isset($_POST['contact_type']) ? sanitize_text_field($_POST['contact_type']) : '';
            
            if ($contact_type === 'email') {
                $contact_value = isset($_POST['contact_value']) ? sanitize_email($_POST['contact_value']) : '';
            } else {
                $contact_value = isset($_POST['contact_value']) ? sanitize_text_field($_POST['contact_value']) : '';
            }

            if (empty($contact_value)) {
                wp_send_json_error(array('message' => 'Contact information is required.'));
                return;
            }

            $subject     = 'New Dubai Business Setup Quote Request';
            $message     = "You have received a new quote request.\n\n";
            $message    .= "Activity Type: $activity_type\n";
            $message    .= "Comments: $comments\n";
            $message    .= "Office Requirement: $office_req\n";
            $message    .= "Shareholders: $shareholders\n";
            $message    .= "Employees: $employees\n";
            $message    .= "Contact Type: $contact_type\n";
            $message    .= "Contact Value: $contact_value\n";

            wp_mail($admin_email, $subject, $message);
            wp_send_json_success(array('message' => 'Thank you! Your request has been received.'));
        }
    }
}

new Custom_Plugin_Calculator();
