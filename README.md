# FAQ List Moodle Plugin

The FAQ List plugin allows Moodle administrators to create and manage FAQ lists containing questions and answers. These FAQs can be displayed in various formats (text, accordion, carousel) as a block on the Moodle website.

## Features

### Display Options

- **Default:** Questions are displayed as headers, and answers as text.
- **Accordion:** Collapsible dropdown format for interactive display.
- **Carousel:** Dynamic sliding display for a modern, visual presentation.

### Block Settings

- Choose to display:
  - Block title.
  - FAQ list title.
  - No title.
- Configure the block appearance and functionality directly within the Moodle interface.
- Place blocks in any defined block area (main, sidebar, etc.).
- Multiple blocks can be placed on the same page, each displaying a different FAQ list.

### Admin Settings

Accessible via **Site Administration -> General -> FAQ Lists**, the plugin offers:

1. **FAQ List Manage**:
   - Create and edit FAQ lists.
   - Update titles and translations.
2. **FAQ List Items Manage (Questions and Answers)**:
   - Manage the most recently edited FAQ list.
   - Add, edit, or delete questions and answers.
   - Change the order of FAQ items for customization.

### Multilingual Support

- FAQ titles and items (questions and answers) can be translated into multiple languages.
- Admin interface includes tabs for each installed language pack, making translation simple and intuitive.

## Installation

1. Clone or download the repository into the appropriate Moodle directory:

```
git clone https://github.com/AGILEDROP/moodle-block_faq_list.git faq_list
```

2. In Moodle, navigate to **Site Administration -> Notifications** and follow the prompts to install the plugin.

## Usage

1. Navigate to **Site Administration -> General -> FAQ Lists**.
2. Under FAQ List Manage:
   - Create and configure FAQ lists.
3. Add questions and answers to the newly created FAQ list.
4. Configure a block on your Moodle page to display the FAQ content:
   - Add a block and select "FAQ List."
   - Choose the display format and title options in block settings.

## Contributing

We welcome contributions to enhance the FAQ List plugin. Here's how you can contribute:

- **Report Issues:** Found a bug or want to suggest a feature? [Open an issue on GitHub](https://github.com/AGILEDROP/moodle-block_faq_list/issues/new).
- **Submit Code:** [Fork the repository](https://github.com/AGILEDROP/moodle-block_faq_list/fork), make changes, and create a pull request for review.
- **Translate:** Contribute translations via the [Moodle AMOS translator](https://lang.moodle.org/local/amos/view.php).
