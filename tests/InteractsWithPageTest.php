<?php

class InteractsWithPageTest extends \SeleniumTestCase
{

    public function setUp()
    {
        parent::setUp();

//        Artisan::call('migrate');
    }

    public function tearDown()
    {
//        Artisan::call('migrate:reset');

        parent::tearDown();
    }

    /** @test */
    public function it_can_visit_a_given_uri()
    {
        $this->visit('/login');
    }

    /** @test */
    public function it_can_visit_the_home_page()
    {
        $this->visit('/');
    }

    /** @test */
    public function it_can_assert_that_the_current_page_url_matches_the_given_uri()
    {
        $this->visit('/login')
            ->seePageIs('login');
    }

    /** @test */
    public function it_can_assert_that_the_current_page_url_matches_the_home_uri()
    {
        $this->visit('/')
            ->seePageIs('/');
    }

    /** @test */
    public function it_can_assert_that_the_current_html_document_contains_the_given_string()
    {
        $this->visit('/login')
            ->see('<label for="email" class="col-md-4 control-label">E-Mail Address</label>');
    }

    /** @test */
    public function it_can_assert_that_the_current_html_document_does_not_contain_the_given_string()
    {
        $this->visit('/login')
            ->dontSee('<span>Nothing to see here</span>');
    }

    /** @test */
    public function it_can_assert_that_an_element_is_seen_on_the_page_with_the_given_css_selector()
    {
        $this->visit('/login')
            ->seeElement('#app')
            ->seeElement('.panel-heading')
            ->seeElement('form')
            ->seeElement('input#email')
            ->seeElement('label.control-label')
            ->seeElement('input[type="email"]');
    }

    /** @test */
    public function it_can_assert_that_an_element_is_seen_on_the_page_with_the_given_name_attribute()
    {
        $this->visit('/login')
            ->seeElement('remember');
    }

    /** @test */
    public function it_can_assert_that_an_element_is_seen_on_the_page_with_the_given_id_attribute()
    {
        $this->visit('/login')
            ->seeElement('app');
    }

    /** @test */
    public function it_can_assert_that_an_element_is_seen_on_the_page_with_the_given_class_attribute()
    {
        $this->visit('/login')
            ->seeElement('control-label');
    }

    /** @test */
    public function it_can_assert_that_an_element_is_not_seen_on_the_page_with_the_given_css_selector()
    {
        $this->visit('/login')
            ->dontSeeElement('#nothingToSee')
            ->dontSeeElement('.nothing-to-see')
            ->dontSeeElement('input#nothingToSee')
            ->dontSeeElement('label.nothing-to-see')
            ->dontSeeElement('input[type="color"]')
            ->dontSeeElement('video');
    }

    /** @test */
    public function it_can_assert_that_an_element_is_not_seen_on_the_page_with_the_given_name_id_or_class_attribute_or_the_tag(
    )
    {
        $this->visit('/login')
            ->dontSeeElement('nothing-to-see');
    }

    /** @test */
    public function it_can_assert_that_the_given_text_is_seen_on_the_page()
    {
        $this->visit('/login')
            ->seeText('E-Mail Address')
            ->seeText('Password');
    }

    /** @test */
    public function it_can_assert_that_the_given_text_is_not_seen_on_the_page()
    {
        $this->visit('/login')
            ->dontSeeText('Nothing to see here');
    }

    /** @test */
    public function it_can_assert_that_an_element_is_seen_on_the_page_with_the_given_css_selector_and_containing_the_given_text(
    )
    {
        $this->visit('/login')
            ->seeInElement('#app-navbar-collapse', 'Register')
            ->seeInElement('.panel-heading', 'Login')
            ->seeInElement('.control-label', 'E-Mail Address')
            ->seeInElement('div#app-navbar-collapse', 'Register')
            ->seeInElement('div.panel-heading', 'Login')
            ->seeInElement('label.control-label', 'E-Mail Address')
            ->seeInElement('p', 'Lorem ipsum dolor sit amet')
            ->seeInElement('#passwordLabel', 'Password');
    }

    /** @test */
    public function it_can_assert_that_an_element_is_seen_on_the_page_with_the_given_name_attribute_and_containing_the_given_text(
    )
    {
        $this->visit('/login')
            ->seeInElement('submit', 'Login');
    }

    /** @test */
    public function it_can_assert_that_an_element_is_seen_on_the_page_with_the_given_id_attribute_and_containing_the_given_text(
    )
    {
        $this->visit('/login')
            ->seeInElement('app-navbar-collapse', 'Register');
    }

    /** @test */
    public function it_can_assert_that_an_element_is_seen_on_the_page_with_the_given_class_attribute_and_containing_the_given_text(
    )
    {
        $this->visit('/login')
            ->seeInElement('panel-heading', 'Login');
    }

    /** @test */
    public function it_can_assert_that_an_element_is_not_seen_on_the_page_with_the_given_css_selector_and_containing_the_given_text(
    )
    {
        $this->visit('/login')
            ->dontSeeInElement('#app-navbar-collapse', 'Nothing')
            ->dontSeeInElement('.panel-heading', 'Not a heading')
            ->dontSeeInElement('.control-label', 'Not a label')
            ->dontSeeInElement('div#app-navbar-collapse', 'Nothing')
            ->dontSeeInElement('div.panel-heading', 'Not a heading')
            ->dontSeeInElement('label.control-label', 'Not a label')
            ->dontSeeInElement('p', 'Nothing to see here');
    }

    /** @test */
    public function it_can_assert_that_an_element_is_not_seen_on_the_page_with_the_given_name_attribute_and_containing_the_given_text(
    )
    {
        $this->visit('/login')
            ->dontSeeInElement('submit', 'Nothing');
    }

    /** @test */
    public function it_can_assert_that_an_element_is_not_seen_on_the_page_with_the_given_id_attribute_and_containing_the_given_text(
    )
    {
        $this->visit('/login')
            ->dontSeeInElement('app-navbar-collapse', 'Nothing');
    }

    /** @test */
    public function it_can_assert_that_an_element_is_not_seen_on_the_page_with_the_given_class_attribute_and_containing_the_given_text(
    )
    {
        $this->visit('/login')
            ->dontSeeInElement('panel-heading', 'Not a heading');
    }

    /** @test */
    public function it_can_assert_that_a_link_is_seen_on_the_page_with_the_given_link_text()
    {
        $this->visit('/login')
            ->seeLink('Visit Laravel')
            ->seeLink('Visit PHP');
    }

    /** @test */
    public function it_can_assert_that_a_link_is_seen_on_the_page_with_the_given_link_text_and_full_url()
    {
        $this->visit('/login')
            ->seeLink('Visit Laravel', 'https://laravel.com');
    }

    /** @test */
    public function it_can_assert_that_a_link_is_seen_on_the_page_with_the_given_link_text_and_full_url_with_trailing_slash(
    )
    {
        $this->visit('/login')
            ->seeLink('Visit Laravel', 'https://laravel.com/');
    }

    /** @test */
    public function it_can_assert_that_a_link_is_seen_on_the_page_with_the_given_link_text_and_partial_url()
    {
        $this->visit('/login')
            ->seeLink('Visit Laravel', 'laravel.com');
    }

    /** @test */
    public function it_can_assert_that_a_link_is_seen_on_the_page_with_the_given_link_text_and_partial_url_with_trailing_slash(
    )
    {
        $this->visit('/login')
            ->seeLink('Visit Laravel', 'laravel.com/');
    }

    /** @test */
    public function it_can_assert_that_a_link_is_not_seen_on_the_page_with_the_given_link_text()
    {
        $this->visit('/login')
            ->dontSeeLink('No link here');
    }

    /** @test */
    public function it_can_assert_that_a_link_is_not_seen_on_the_page_with_the_invalid_link_text_and_valid_full_url()
    {
        $this->visit('/login')
            ->dontSeeLink('No link here', 'https://laravel.com');
    }

    /** @test */
    public function it_can_assert_that_a_link_is_not_seen_on_the_page_with_the_valid_link_text_and_invalid_full_url()
    {
        $this->visit('/login')
            ->dontSeeLink('Visit Laravel', 'https://not-laravel.com');
    }

    /** @test */
    public function it_can_assert_that_an_input_field_with_the_given_name_attribute_contains_the_given_text()
    {
        $this->visit('/form')
            ->seeInField('text-input-with-value-1', 'Text input with value')
            ->seeInField('text-area-with-value-1', 'Text area with value');
    }

    /** @test */
    public function it_can_assert_that_an_input_field_with_the_given_id_attribute_contains_the_given_text()
    {
        $this->visit('/form')
            ->seeInField('#textinputwithvalue1', 'Text input with value')
            ->seeInField('textinputwithvalue1', 'Text input with value')
            ->seeInField('#textareawithvalue1', 'Text area with value')
            ->seeInField('textareawithvalue1', 'Text area with value');

    }

    /** @test */
    public function it_can_assert_that_an_input_field_with_the_given_name_attribute_does_not_contain_the_given_text()
    {
        $this->visit('/form')
            ->dontSeeInField('text-input-with-value-1', 'Nothing to see here');
    }

    /** @test */
    public function it_can_assert_that_an_input_field_with_the_given_id_attribute_does_not_contain_the_given_text()
    {
        $this->visit('/form')
            ->dontSeeInField('#textinputwithvalue1', 'Nothing to see here')
            ->dontSeeInField('textinputwithvalue1', 'Nothing to see here');
    }

    /** @test */
    public function it_can_assert_that_a_select_field_with_the_given_name_attribute_and_given_value_is_selected()
    {
        $this->visit('/form')
            ->seeIsSelected('select-1', 'bos');
    }

    /** @test */
    public function it_can_assert_that_a_select_field_with_the_given_id_attribute_and_given_value_is_selected()
    {
        $this->visit('/form')
            ->seeIsSelected('#select1', 'bos')
            ->seeIsSelected('select1', 'bos');
    }

    /** @test */
    public function it_can_assert_that_a_select_field_with_the_given_name_attribute_and_given_value_is_not_selected()
    {
        $this->visit('/form')
            ->dontSeeIsSelected('select-1', 'ams')
            ->dontSeeIsSelected('select-1', 'atl')
            ->dontSeeIsSelected('select-1', 'bal')
            ->dontSeeIsSelected('select-1', 'bue');
    }

    /** @test */
    public function it_can_assert_that_a_select_field_with_the_given_id_attribute_and_given_value_is_not_selected()
    {
        $this->visit('/form')
            ->dontSeeIsSelected('#select1', 'bal')
            ->dontSeeIsSelected('select1', 'bal');
    }

    /** @test */
    public function it_can_assert_that_a_radio_group_with_the_given_name_attribute_and_given_value_is_selected()
    {
        $this->visit('/form')
            ->seeIsSelected('radio-group-3', 'square');
    }

    /** @test */
    public function it_can_assert_that_a_radio_group_with_the_given_name_attribute_and_given_value_is_not_selected()
    {
        $this->visit('/form')
            ->dontSeeIsSelected('radio-group-3', 'circle')
            ->dontSeeIsSelected('radio-group-3', 'triangle')
            ->dontSeeIsSelected('radio-group-3', 'rectangle');
    }

    /** @test */
    public function it_can_assert_that_a_multi_select_field_with_the_given_name_attribute_and_given_values_are_selected(
    )
    {
        $this->visit('/form')
            ->seeIsSelected('multi-select-1', 'atl')
            ->seeIsSelected('multi-select-1', 'chi');
    }

    /** @test */
    public function it_can_assert_that_a_multi_select_field_with_the_given_id_attribute_and_given_value_are_selected()
    {
        $this->visit('/form')
            ->seeIsSelected('#multiselect1', 'atl')
            ->seeIsSelected('multiselect1', 'atl')
            ->seeIsSelected('#multiselect1', 'chi')
            ->seeIsSelected('multiselect1', 'chi');
    }

    /** @test */
    public function it_can_assert_that_a_multi_select_field_with_the_given_name_attribute_and_given_value_are_not_selected(
    )
    {
        $this->visit('/form')
            ->dontSeeIsSelected('multi-select-1', 'ams')
            ->dontSeeIsSelected('multi-select-1', 'bal')
            ->dontSeeIsSelected('multi-select-1', 'bos')
            ->dontSeeIsSelected('multi-select-1', 'bue')
            ->dontSeeIsSelected('multi-select-1', 'cal');
    }

    /** @test */
    public function it_can_assert_that_a_checkbox_with_the_given_name_attribute_is_checked()
    {
        $this->visit('/form')
            ->seeIsChecked('checkbox-8');
    }

    /** @test */
    public function it_can_assert_that_a_checkbox_with_the_given_id_attribute_is_checked()
    {
        $this->visit('/form')
            ->seeIsChecked('#checkbox8')
            ->seeIsChecked('checkbox8');
    }

    /** @test */
    public function it_can_assert_that_multiple_checkboxes_with_the_given_name_attributes_are_checked()
    {
        $this->visit('/form')
            ->seeIsChecked('checkbox-8')
            ->seeIsChecked('checkbox-10');
    }

    /** @test */
    public function it_can_assert_that_multiple_checkboxes_with_the_given_id_attributes_are_checked()
    {
        $this->visit('/form')
            ->seeIsChecked('#checkbox8')
            ->seeIsChecked('checkbox8')
            ->seeIsChecked('#checkbox10')
            ->seeIsChecked('checkbox10');
    }

    /** @test */
    public function it_can_assert_that_a_checkbox_with_the_given_name_attribute_is_not_checked()
    {
        $this->visit('/form')
            ->dontSeeIsChecked('checkbox-7')
            ->dontSeeIsChecked('checkbox-9');
    }

    /** @test */
    public function it_can_assert_that_a_checkbox_with_the_given_id_attribute_is_not_checked()
    {
        $this->visit('/form')
            ->dontSeeIsChecked('#checkbox7')
            ->dontSeeIsChecked('checkbox7')
            ->dontSeeIsChecked('#checkbox9')
            ->dontSeeIsChecked('checkbox9');
    }

    /** @test */
    public function it_can_assert_that_an_element_with_the_given_name_attribute_is_disabled()
    {
        $this->visit('/form')
            ->seeIsDisabled('disabled-text-input-1');
    }

    /** @test */
    public function it_can_assert_that_an_element_with_the_given_id_attribute_is_disabled()
    {
        $this->visit('/form')
            ->seeIsDisabled('#disabledtextinput1')
            ->seeIsDisabled('disabledtextinput1');
    }

    /** @test */
    public function it_can_assert_that_an_element_with_the_given_name_attribute_is_not_disabled()
    {
        $this->visit('/form')
            ->dontSeeIsDisabled('text-input-1');
    }

    /** @test */
    public function it_can_assert_that_an_element_with_the_given_id_attribute_is_not_disabled()
    {
        $this->visit('/form')
            ->dontSeeIsDisabled('#textinput1')
            ->dontSeeIsDisabled('textinput1');
    }

    /** @test */
    public function it_can_click_a_given_link_using_the_link_text()
    {
        $this->visit('/form')
            ->click('Register');
    }

    /** @test */
    public function it_can_click_a_given_link_using_an_id_attribute()
    {
        $this->visit('/form')
            ->click('#registerLinkId')
            ->click('registerLinkId');
    }

    /** @test */
    public function it_can_type_text_into_an_input_using_an_name_attribute()
    {
        $this->visit('/form')
            ->type('Some test text', 'text-input-1');
    }

    /** @test */
    public function it_can_type_text_into_an_input_using_an_id_attribute()
    {
        $this->visit('/form')
            ->type('Some test text', '#textinput1');
    }

    /** @test */
    public function it_can_type_text_into_a_textarea_using_an_name_attribute()
    {
        $this->visit('/form')
            ->type('Some test text', 'textarea-1');
    }

    /** @test */
    public function it_can_type_text_into_a_textarea_using_an_id_attribute()
    {
        $this->visit('/form')
            ->type('Some test text', '#textarea1');
    }

    /** @test */
    public function it_can_clear_text_from_an_input_using_an_name_attribute()
    {
        $this->visit('/form')
            ->clear('text-input-with-value-1');
    }

    /** @test */
    public function it_can_clear_text_from_an_input_using_an_id_attribute()
    {
        $this->visit('/form')
            ->clear('#textinputwithvalue1');
    }

    /** @test */
    public function it_can_clear_text_from_a_textarea_using_an_name_attribute()
    {
        $this->visit('/form')
            ->clear('text-area-with-value-1');
    }

    /** @test */
    public function it_can_clear_text_from_a_textarea_using_an_id_attribute()
    {
        $this->visit('/form')
            ->clear('#textareawithvalue1');
    }

    /** @test */
    public function it_can_check_a_given_checkbox_using_an_name_attribute()
    {
        $this->visit('/form')
            ->check('checkbox-2')
            ->seeIsChecked('checkbox-2');
    }

    /** @test */
    public function it_can_check_a_given_checkbox_using_an_id_attribute()
    {
        $this->visit('/form')
            ->check('#checkbox2')
            ->check('checkbox3')
            ->seeIsChecked('#checkbox2')
            ->seeIsChecked('checkbox3');
    }

    /** @test */
    public function it_can_uncheck_a_given_checkbox_using_an_name_attribute()
    {
        $this->visit('/form')
            ->uncheck('checkbox-8')
            ->dontSeeIsChecked('checkbox-8');
    }

    /** @test */
    public function it_can_uncheck_a_given_checkbox_using_an_id_attribute()
    {
        $this->visit('/form')
            ->uncheck('#checkbox8')
            ->uncheck('checkbox10')
            ->dontSeeIsChecked('#checkbox8')
            ->dontSeeIsChecked('checkbox10');
    }

    /** @test */
    public function it_can_select_a_given_option_from_a_drop_down_using_an_name_attribute()
    {
        $this->visit('/form')
            ->select('Baltimore', 'select-1')
            ->seeIsSelected('select-1', 'bal');
    }

    /** @test */
    public function it_can_select_a_given_option_from_a_drop_down_using_an_id_attribute()
    {
        $this->visit('/form')
            ->select('Baltimore', '#select1')
            ->select('Boston', 'select1')
            ->seeIsSelected('select-1', 'bos');
    }

    /** @test */
    public function it_can_select_multiple_options_from_a_multiselect_using_an_name_attribute()
    {
        $this->visit('/form')
            ->select('Baltimore', 'multi-select-1')
            ->select('Calgary', 'multi-select-1')
            ->dontSeeIsSelected('multi-select-1', 'ams')
            ->seeIsSelected('multi-select-1', 'atl')
            ->seeIsSelected('multi-select-1', 'bal')
            ->dontSeeIsSelected('multi-select-1', 'bos')
            ->dontSeeIsSelected('multi-select-1', 'bue')
            ->seeIsSelected('multi-select-1', 'cal')
            ->seeIsSelected('multi-select-1', 'chi');
    }

    /** @test */
    public function it_can_select_multiple_options_from_a_multiselect_using_an_id_attribute()
    {
        $this->visit('/form')
            ->select('Baltimore', '#multiselect1')
            ->select('Calgary', 'multiselect1')
            ->dontSeeIsSelected('multi-select-1', 'ams')
            ->seeIsSelected('multi-select-1', 'atl')
            ->seeIsSelected('multi-select-1', 'bal')
            ->dontSeeIsSelected('multi-select-1', 'bos')
            ->dontSeeIsSelected('multi-select-1', 'bue')
            ->seeIsSelected('multi-select-1', 'cal')
            ->seeIsSelected('multi-select-1', 'chi');
    }

    /** @test */
    public function it_can_press_a_given_button_using_the_button_text()
    {
        $this->visit('/form')
            ->press('Submit');
    }

    /** @test */
    public function it_can_press_a_given_button_using_an_name_attribute()
    {
        $this->visit('/form')
            ->press('submit-button-1');
    }

    /** @test */
    public function it_can_press_a_given_button_using_an_id_attribute()
    {
        $this->visit('/form')
            ->press('#submitbutton1')
            ->press('submitbutton1');
    }

    /** @test */
    public function it_can_click_a_given_element_using_the_element_text()
    {
        $this->visit('/form')
            ->clickOnElement('Checkbox 1');
    }

    /** @test */
    public function it_can_click_a_given_element_using_a_name_attribute()
    {
        $this->visit('/form')
            ->clickOnElement('checkbox-1-label');
    }

    /** @test */
    public function it_can_click_a_given_element_using_an_id_attribute()
    {
        $this->visit('/form')
            ->clickOnElement('#checkbox1label')
            ->clickOnElement('checkbox1label');
    }
}