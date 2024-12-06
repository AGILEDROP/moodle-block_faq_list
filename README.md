# FAQ List Plugin for Moodle

The FAQ List plugin allows Moodle administrators to create and manage FAQ lists containing questions and answers. These FAQs can be displayed in various formats (text, accordion, carousel) as a block on the Moodle website, providing a flexible and engaging way to present information.

## Features

### Display Options

- **Text Format:** Questions are displayed as bold `<h3>` elements, and answers as `<p>` tagged text.
- **Accordion:** Collapsible dropdown format for interactive display.
- **Carousel:** Dynamic sliding display for a modern, visual presentation.

### Block Settings

- Choose to display:
  - Block title.
  - FAQ list title.
  - No title.
- Configure the block appearance and functionality directly within the Moodle interface.

### Admin Settings

Accessible via Site Administration -> General -> FAQ Lists, the plugin offers:

1. FAQ List Manage:
   - Create and edit FAQ lists.
   - Update titles and translations.
   - Add, edit, or delete questions and answers. 
2. FAQ List Items Manage (Questions and Answers):
   - Manage the most recently edited FAQ list.

## Installation

1. Clone or download the repository into the appropriate Moodle directory:

```git clone https://github.com/AGILEDROP/moodle-block_faq_list.git faq_list```

2. In Moodle, navigate to Site Administration -> Notifications and follow the prompts to install the plugin.

## Usage

1. Navigate to Site Administration -> General -> FAQ Lists.
2. Under FAQ List Manage:
   - Create and configure FAQ lists. 
   - Add translations for multilingual support.
3. Configure a block on your Moodle page to display the FAQ content:
   - Add a block and select "FAQ List."
   - Choose the display format and title options in block settings.

## Contributing

We welcome contributions to enhance the FAQ List plugin. Here's how you can contribute:

- **Report Issues:** Found a bug or want to suggest a feature? Open an issue on GitHub.
- **Submit Code:** Fork the repository, make changes, and create a pull request for review.
- **Translate:** Contribute translations via the Moodle AMOS translator.
