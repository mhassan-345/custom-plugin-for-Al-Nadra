# Cost Calculation Form

A custom WordPress plugin designed as a sophisticated lead-capture tool for a Dubai business setup consultancy. It provides responsive, interactive forms that can be easily integrated using shortcodes or directly via the Avada Fusion Builder.

## Key Features

- **Multi-form Functionality**: Includes three built-in form types:
  - **Business Setup Cost Calculator**: Collects user requirements (activity type, office needs, visas/shareholders) to provide a tailored quote.
  - **Expert Support Form**: A quick contact form for expert assistance.
  - **Pricing Grid with Popup**: A visually appealing grid of common business setups with a modal/popup contact form for quick inquiries.
- **Avada Fusion Builder Integration**: Registers directly into the Avada Builder as a custom element ("Custom Forms") for seamless drag-and-drop page building.
- **AJAX Submissions**: Form submissions are handled securely and asynchronously without page reloads.
- **Responsive & Modern UI**: Built with pure PHP, semantic HTML, CSS, and vanilla JavaScript, ensuring a professional, premium user experience.
- **WhatsApp Integration**: Direct one-click WhatsApp contact support.

## How to Install and Use

### Installation
1. Download or clone this repository.
2. Upload the `Cost-Calculation-form` folder to your WordPress installation's `/wp-content/plugins/` directory.
3. Activate the plugin through the **Plugins** menu in the WordPress dashboard.

### Usage
#### Shortcode
You can place the form anywhere on your site using the provided shortcode:
```text
[dubai_setup_calculator]
```
You can specify the form type using the `form_type` parameter:
- `[dubai_setup_calculator form_type="calculator"]` (Default)
- `[dubai_setup_calculator form_type="expert_support"]`
- `[dubai_setup_calculator form_type="pricing_grid"]`

#### Avada Fusion Builder
If you are using the Avada theme, simply edit a page with the Fusion Builder, click **+ Element**, and search for **Custom Forms**. You can then easily select the desired form type from the dropdown configuration.

## Development Stack
- PHP (Native WordPress Hooks & AJAX implementation)
- Vanilla JavaScript
- Modern CSS 
