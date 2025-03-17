<?php

namespace Drupal\contact_form\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Mail\MailManagerInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a custom quote request form.
 */
class ContactForm extends FormBase {

  /**
   * Mail manager service.
   *
   * @var \Drupal\Core\Mail\MailManagerInterface
   */
  protected $mailManager;

  /**
   * Messenger service.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected $messenger;

  /**
   * Constructs a ContactForm object.
   */
  public function __construct(MailManagerInterface $mail_manager, MessengerInterface $messenger) {
    $this->mailManager = $mail_manager;
    $this->messenger = $messenger;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('plugin.manager.mail'),
      $container->get('messenger')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'custom_quote_request_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    // Add an AJAX wrapper to the entire form
    $form['#prefix'] = '<div id="form-container">';
    $form['#suffix'] = '</div>';

$form['ajax_wrapper'] = [
    '#type' => 'container',
    '#attributes' => ['id' => 'ajax-wrapper'],
];


    
$form['ajax_wrapper']['name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Your Name'),
      '#required' => TRUE,
    ];

$form['ajax_wrapper']['contact_info'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Email or Phone Number'),
      '#required' => TRUE,
    ];


$form['ajax_wrapper']['hull_type'] = [
  '#type' => 'radios',
  '#title' => $this->t('Hull Type'),
  '#description' => $this->t('Select the type of hull you want.'),
  '#options' => [
    '400000' => $this->t('SS 349 Hull $400,000 <span class="deets">Vaccume Infused Epoxy Fiberglass</span>'),
    '410000' => $this->t('SS 349 Carbon +10,000 <span class="deets">Vaccume Infused Epoxy Carbon/Glass Hybrid</span>'),
    '650000' => $this->t('Starship 349 Forged Carbon +$250,000 <span class="deets">Vaccume Infused Epoxy Carbon/Glass Hybrid with Visual hull sides and deck forged carbon. (Premium Package Only Available with Premium options below)</span>'),
  ],
  '#default_value' => '400000',
  '#ajax' => [
    'callback' => '::updateFormFields', // Triggers the update function
    'wrapper' => 'form-container', // Ensure your form has an ID for AJAX
    'event' => 'change',
  ],
  '#options_attributes' => [
    '400000' => ['class' => ['standard-hull-option']],
    '410000' => ['class' => ['carbon-fiber-option']],
    '650000' => ['class' => ['forged-carbon-option']],
  ],
];
    
$form['ajax_wrapper']['color_design'] = [
  '#type' => 'radios',
  '#title' => $this->t('Color & Design'),
  '#description' => $this->t('Color Design Details'),
  '#options' => [
    '0' => $this->t('Standard Base Color + 3 Color Graphic Gel Design <span class="deets"></span>'),
    '20000' => $this->t('Optional + Custom Multicolor Paint +$20,000 <span class="deets">Designed to your needs</span>'),
  ],
  '#default_value' => '0',
  '#ajax' => [
    'callback' => '::updateQuoteTotal',
    'wrapper' => 'quote-total',
    'event' => 'change',
  ],
  '#attributes' => ['class' => ['BG_color']],
];


$form['ajax_wrapper']['power'] = [
  '#type' => 'radios',
  '#title' => $this->t('Power'),
  '#description' => $this->t('Power Details'),
  '#options' => [
    '0' => $this->t('Standard - Mercury 300 R DTS'),
    '40000' => $this->t('Stage 2 - Mercury 400 R +$40,000'),
    '50000' => $this->t('Stage 3 - Mercury 500 R +$50,000'),
  ],
  '#default_value' => '0',
  '#ajax' => [
    'callback' => '::updateQuoteTotal',
    'wrapper' => 'quote-total',
    'event' => 'change',
  ],
  '#attributes' => ['class' => ['BG_power']],
];

$form['ajax_wrapper']['steering'] = [
      '#type' => 'radios',
      '#title' => $this->t('Steering'),
      '#description' => $this->t('Helm Options Details'),
      '#options' => [
        '0' => $this->t('Standard Imco 7 Tilt Helm'),
        '200' => $this->t('Optional Black Aluminum Helm +$200'),
        '220' => $this->t('Optional Isatta Finette Wheel Helm +$220'),
      ],
      '#default_value' => '0',
      '#ajax' => [
        'callback' => '::updateQuoteTotal',
        'wrapper' => 'quote-total',
        'event' => 'change',
  ], '#attributes' => [ 'class' => ['BG_steering'], ],
    ];

$form['ajax_wrapper']['interior'] = [
  '#type' => 'checkboxes',
  '#title' => $this->t('Interior'),
  '#description' => $this->t('Interior Design Details'),
  '#options' => [
    '0' => $this->t('Standard - 3 Color Cool Touch Vinyl w Diamond or Pleated'),
    '4500' => $this->t('Optional - Alcantara Seats +$4,500'),
    '12500' => $this->t('Optional - Full Alcantara +$12,500'),
    '1200' => $this->t('Optional - EVA Foam 3 Color Match +$1,200'),
    '3500' => $this->t('Optional - Cockpit Cover +$3,500'),
    '2002' => $this->t('Optional - Boat Cover +$2,002'),
  ],
  '#default_value' => ['0'], // Default to Standard option
  '#ajax' => [
    'callback' => '::updateQuoteTotal',
    'wrapper' => 'quote-total',
    'event' => 'change',
  ],
  '#attributes' => ['class' => ['BG_interior']],
];

$form['ajax_wrapper']['stereo'] = [
    '#type' => 'radios',
    '#title' => $this->t('Stereo'),
    '#description' => $this->t('Audio Design Details'),
    '#options' => [
        '0' => $this->t('Do it Yourself / Not Included ($0)'),
        '6000' => $this->t('Optional Wetsounds Package ($6,000)'),
    ],
    '#default_value' => '0',
    '#ajax' => [
        'callback' => '::updateQuoteTotal',
        'wrapper' => 'quote-total',
        'event' => 'change',
    ],
    '#attributes' => ['class' => ['BG_stereo']],
];
      

$form['ajax_wrapper']['electronics'] = [
    '#type' => 'radios',
    '#title' => $this->t('Electronics'),
    '#description' => $this->t('Electronics Details'),
    '#options' => [
        '0' => $this->t('2 Simrad Screens (Centered in Dash) (Included)'),
        '10500' => $this->t('+3 Simrad Screens (Full Screen Dash Look) ($10,500)'),
    ],
    '#default_value' => '0',
    '#ajax' => [
        'callback' => '::updateQuoteTotal',
        'wrapper' => 'quote-total',
        'event' => 'change',
    ],
    '#attributes' => ['class' => ['BG_electronics']],
];

$form['ajax_wrapper']['rigging'] = [
    '#type' => 'checkboxes',
    '#title' => $this->t('Rigging'),
    '#description' => $this->t('Rigging Details'),
    '#options' => [
        '0' => $this->t('Standard - see list ($0)'),
        '750' => $this->t('Optional - Billet Fender Holders ($750)'),
        '900' => $this->t('Add + Additional Battery + Box ($900)'),
        '1400' => $this->t('Add + Transom LED Lights ($1,400)'),
        '1200' => $this->t('Add + Under Bow Rock Lights x 8 ($1,200)'),
    ],
    '#default_value' => ['0'],
    '#ajax' => [
        'callback' => '::updateQuoteTotal',
        'wrapper' => 'quote-total',
        'event' => 'change',
    ],
    '#attributes' => ['class' => ['BG_rigging']],
];

    
$form['ajax_wrapper']['quote_total'] = [
    '#type' => 'markup',
    '#prefix' => '<div id="quote-total-wrapper">',
    '#suffix' => '</div>',
    '#markup' => '
        <div id="quote-total" class="QuotetotalzHere BStroke TotalBlockFloat text-center">
            <div class="totaltitle text-16 bold">Estimated Cost</div>
            <div class="totalprize bold text-36">$400,000</div>
        </div>
    ',
];

$form['ajax_wrapper']['additional_notes'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Additional Notes or Requests'),
    ];

$form['ajax_wrapper']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Request Quote'),
    ];

    return $form;
    
  }

  public function updateQuoteTotal(array &$form, FormStateInterface $form_state) {
 return $form['ajax_wrapper'];

    // Process checkbox values for Rigging & Interior
    $rigging_values = $form_state->getValue('rigging') ?: [];
    $rigging_total = array_sum(array_map('intval', array_filter($rigging_values)));

    $interior_values = $form_state->getValue('interior') ?: [];
    $interior_total = array_sum(array_map('intval', array_filter($interior_values)));

    $total_price = array_sum([
        (int) $form_state->getValue('hull_type'),
        (int) $form_state->getValue('color_design'),
        (int) $form_state->getValue('power'),
        (int) $form_state->getValue('steering'),
        (int) $form_state->getValue('electronics'),
        (int) $form_state->getValue('stereo'),
        $rigging_total,
        $interior_total,
    ]);

    // Add this: Update hull_type class based on selection
    $hull_type = $form_state->getValue('hull_type');
    $form['hull_type']['#attributes']['class'] = [$hull_type === '450000' ? 'BGHull_Flip2' : 'BG_hull'];
    
    $form['quote_total']['#markup'] = '
        <div id="quote-total" class="QuotetotalzHere BStroke TotalBlockFloat text-center">
            <div class="totaltitle text-16 bold">Estimated Cost</div>
            <div class="totalprize bold text-36">$' . number_format($total_price, 2) . '</div>
        </div>
    ';
    
    return $form['quote_total']; // Ensure this returns correctly
  }

 public function updateFormFields(array &$form, FormStateInterface $form_state) {
    $selected_hull = $form_state->getValue('hull_type');

    // If hull_type option 3 (650000) is selected, apply forced selections
    if ($selected_hull == '650000') {
        $form['color_design']['#default_value'] = '20000';
        $form['color_design']['#disabled'] = TRUE;

        $form['power']['#default_value'] = '50000';
        $form['power']['#disabled'] = TRUE;

        $form['stereo']['#default_value'] = '6000';
        $form['stereo']['#disabled'] = TRUE;

        $form['electronics']['#default_value'] = '10500';
        $form['electronics']['#disabled'] = TRUE;

        // Set and disable checkboxes individually
        $form['interior']['#default_value'] = ['4500', '12500', '1200', '3500'];
        foreach (['4500', '12500', '1200', '3500'] as $option) {
            $form['interior']['#options'][$option] = [
                'disabled' => TRUE,
            ];
        }

        $form['rigging']['#default_value'] = ['750', '900', '1400', '1200'];
        foreach (['750', '900', '1400', '1200'] as $option) {
            $form['rigging']['#options'][$option] = [
                'disabled' => TRUE,
            ];
        }
    } else {
        // Restore all fields if a different hull type is selected
        $form['color_design']['#disabled'] = FALSE;
        $form['power']['#disabled'] = FALSE;
        $form['stereo']['#disabled'] = FALSE;
        $form['electronics']['#disabled'] = FALSE;

        // Enable checkboxes individually
        foreach (['4500', '12500', '1200', '3500'] as $option) {
            $form['interior']['#options'][$option] = [
                'disabled' => FALSE,
            ];
        }

        foreach (['750', '900', '1400', '1200'] as $option) {
            $form['rigging']['#options'][$option] = [
                'disabled' => FALSE,
            ];
        }
    }

    return $form;
}



public function submitForm(array &$form, FormStateInterface $form_state) {
    $to = 'hbdsu4appvz4@p3plzcpnl508915.prod.phx3.secureserver.net';

    // Recalculate rigging and interior totals
    $rigging_values = $form_state->getValue('rigging') ?: [];
    $rigging_total = array_sum(array_map('intval', array_filter($rigging_values)));

    $interior_values = $form_state->getValue('interior') ?: [];
    $interior_total = array_sum(array_map('intval', array_filter($interior_values)));

    // Calculate total price
    $total_price = array_sum([
        (int) $form_state->getValue('hull_type'),
        (int) $form_state->getValue('color_design'),
        (int) $form_state->getValue('power'),
        (int) $form_state->getValue('steering'),
        (int) $form_state->getValue('electronics'),
        (int) $form_state->getValue('stereo'),
        $rigging_total,
        $interior_total,
    ]);

    $params['subject'] = 'New Quote Request';
    $params['message'] = "Quote Request Details:\n" .
        "--------------------\n" .
        "Name: " . $form_state->getValue('name') . "\n" .
        "Contact Info: " . $form_state->getValue('contact_info') . "\n" .
        "--------------------\n" .
        "Hull Type: " . $form['hull_type']['#options'][$form_state->getValue('hull_type')] . "\n" .
        "Color & Design: " . $form['color_design']['#options'][$form_state->getValue('color_design')] . "\n" .
        "Power: " . $form['power']['#options'][$form_state->getValue('power')] . "\n" .
        "Steering: " . $form['steering']['#options'][$form_state->getValue('steering')] . "\n" .
"Interior: \n" . (!empty($form_state->getValue('interior')) 
    ? implode("\n", array_map(function ($value) use ($form) {
        return "- " . $form['interior']['#options'][$value]; // Adds a dash for better readability
    }, array_filter($form_state->getValue('interior')))) 
    : "None") . "\n\n" .
        "Stereo: " . $form['stereo']['#options'][$form_state->getValue('stereo')] . "\n" .
        "Electronics: " . $form['electronics']['#options'][$form_state->getValue('electronics')] . "\n" .
"Rigging: \n" . (!empty($form_state->getValue('rigging')) 
    ? implode("\n", array_map(function ($value) use ($form) {
        return "- " . $form['rigging']['#options'][$value]; // Adds a dash for better readability
    }, array_filter($form_state->getValue('rigging')))) 
    : "None") . "\n\n" .
        "--------------------\n" .
        "Total Price: $" . number_format($total_price, 2) . "\n" .
        "--------------------\n" .
        "Additional Notes:\n" . ($form_state->getValue('additional_notes') ?: "None") . "\n";

    $this->mailManager->mail('contact_form', 'contact_message', $to, \Drupal::currentUser()->getPreferredLangcode(), $params);
}

}
