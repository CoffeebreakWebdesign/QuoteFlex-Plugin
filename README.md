# QuoteFlex

**Flexible quote management for WordPress**

[![WordPress](https://img.shields.io/badge/WordPress-6.0%2B-blue.svg)](https://wordpress.org/)
[![PHP](https://img.shields.io/badge/PHP-7.4%2B-purple.svg)](https://php.net/)
[![License](https://img.shields.io/badge/License-GPL%20v2-green.svg)](LICENSE)

A powerful WordPress plugin for managing and displaying inspirational quotes with API integration, quote sets, and multiple display options.

---

## Features

- 🔍 **API Search & Import** - Search and import from Quotable.io
- 📦 **Quote Sets** - Organize quotes into curated collections
- ✨ **Duplicate Detection** - Prevent re-importing existing quotes
- ✏️ **Manual Entry** - Add custom quotes
- 🎨 **4 Templates** - Default, Boxed, Card, Minimal
- ⚡ **AJAX Refresh** - Load new quotes without page reload
- 🧩 **Gutenberg Block** - Modern block editor integration
- 📱 **Responsive** - Works on all devices
- 🎯 **100% Free** - No premium upsells

---

## Installation

### For Users

1. Download the latest release from [WordPress.org](https://wordpress.org/plugins/quoteflex/) or [Releases](../../releases)
2. Upload via WordPress Admin → Plugins → Add New → Upload Plugin
3. Activate the plugin
4. Go to QuoteFlex → Search & Import to add your first quotes

### For Developers

```bash
# Clone the repository
git clone https://github.com/yourusername/quoteflex.git

# Navigate to WordPress plugins directory
cd /path/to/wordpress/wp-content/plugins/

# Copy or symlink the plugin
cp -r /path/to/quoteflex ./quoteflex
# OR
ln -s /path/to/quoteflex ./quoteflex

# Activate in WordPress Admin
```

---

## Usage

### Shortcode

Display random quote:
```
[quoteflex]
```

Display from specific set:
```
[quoteflex set="leadership"]
```

Customized display:
```
[quoteflex set="motivation" template="boxed" show_author="yes"]
```

### Gutenberg Block

1. Add new block
2. Search for "QuoteFlex Quote"
3. Configure in sidebar
4. Publish

### Widget

1. Appearance → Widgets
2. Add "QuoteFlex Random Quote"
3. Configure settings

### Template Function

```php
<?php quoteflex_display_quote( array( 'set' => 'homepage' ) ); ?>
```

---

## File Structure

```
quoteflex/
├── quoteflex.php              # Main plugin file
├── readme.txt                 # WordPress.org readme
├── LICENSE                    # GPL v2 license
├── uninstall.php             # Cleanup on uninstall
├── includes/                 # Core classes
├── admin/                    # Admin pages & views
├── public/                   # Frontend (shortcode, block, widget, templates)
├── assets/                   # CSS, JavaScript, images
└── languages/                # Translation files
```

---

## Requirements

- **WordPress:** 6.0+
- **PHP:** 7.4+ (compatible with 8.0, 8.1, 8.2, 8.3)
- **MySQL:** 5.6+

---

## Development

### Database Schema

The plugin creates 4 tables:
- `quoteflex_quotes` - Stores all quotes
- `quoteflex_sets` - Quote set definitions
- `quoteflex_set_relationships` - Many-to-many relationships
- `quoteflex_categories` - Optional categories

### Hooks & Filters

**Actions:**
- `quoteflex_quote_added` - After quote is added
- `quoteflex_quote_updated` - After quote is updated
- `quoteflex_quote_deleted` - After quote is deleted
- `quoteflex_set_created` - After set is created

**Filters:**
- `quoteflex_quote_query_args` - Filter quote query arguments
- `quoteflex_display_template` - Filter template file path
- `quoteflex_quote_html` - Filter quote HTML output

---

## Security

This plugin follows WordPress security best practices:

- ✅ Nonce verification on all forms
- ✅ Capability checks (manage_options)
- ✅ Input sanitization
- ✅ Output escaping
- ✅ Prepared SQL statements
- ✅ AJAX nonce verification

**Security Score: 10/10** ✅

---

## Contributing

This plugin is provided as-is without active maintenance. However, contributions are welcome:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

Please follow WordPress coding standards and include tests for new features.

---

## License

This project is licensed under the GPL v2 or later - see the [LICENSE](LICENSE) file for details.

**DISCLAIMER:** This software is provided "AS IS" without warranty of any kind. The author(s) provide no active support. Use at your own risk.

---

## Credits

- [Quotable.io](https://quotable.io) - Free quote API
- [WordPress](https://wordpress.org/) - CMS platform

---

## Changelog

### Version 1.0.0 (February 6, 2026)
- Initial release
- API integration with Quotable.io
- Quote Sets functionality
- Multiple display templates
- Gutenberg block
- Widget support
- AJAX refresh
- Duplicate detection
- Security hardening

---

## Support

For documentation and tutorials, visit [quoteflex.io](https://quoteflex.io)

**Note:** This plugin is provided as-is without active support or warranty.
