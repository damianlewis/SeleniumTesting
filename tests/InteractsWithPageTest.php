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
            ->seeInField('text-input-with-value', 'Text input with value')
            ->seeInField('text-area-with-value', 'Text area with value');
    }

    /** @test */
    public function it_can_assert_that_an_input_field_with_the_given_id_attribute_contains_the_given_text()
    {
        $this->visit('/form')
            ->seeInField('#textInputWithValue', 'Text input with value')
            ->seeInField('textInputWithValue', 'Text input with value')
            ->seeInField('#textAreaWithValue', 'Text area with value')
            ->seeInField('textAreaWithValue', 'Text area with value');

    }

    /** @test */
    public function it_can_assert_that_an_input_field_with_the_given_name_attribute_does_not_contain_the_given_text()
    {
        $this->visit('/form')
            ->dontSeeInField('text-input-with-value', 'Nothing to see here');
    }

    /** @test */
    public function it_can_assert_that_an_input_field_with_the_given_id_attribute_does_not_contain_the_given_text()
    {
        $this->visit('/form')
            ->dontSeeInField('#textInputWithValue', 'Nothing to see here')
            ->dontSeeInField('textInputWithValue', 'Nothing to see here');
    }

//    /** @test */
//    public function it_can_click_a_given_link_using_the_link_text()
//    {
//        $this->visit('/form')
//            ->click('Register');
//    }
//
//    /** @test */
//    public function it_can_click_a_given_link_using_an_id_css_selector()
//    {
//        $this->visit('/login')
//            ->click('#registerLinkId');
//    }
//
//    /** @test */
//    public function it_can_click_a_given_link_using_an_id_attribute()
//    {
//        $this->visit('/login')
//            ->click('registerLinkId');
//    }
//
//    /** @test */
//    public function it_can_type_text_into_an_input_using_an_id_css_selector()
//    {
//        $this->visit('/login')
//            ->type('Some test text', '#testTextInput');
//    }
//
//    /** @test */
//    public function it_can_type_text_into_an_input_using_a_name_attribute()
//    {
//        $this->visit('/login')
//            ->type('Some test text', 'test-text-input');
//    }
//
//    /** @test */
//    public function it_can_type_text_into_an_input_using_an_id_attribute()
//    {
//        $this->visit('/login')
//            ->type('Some test text', 'testTextInput');
//    }
//
//    /** @test */
//    public function it_can_type_text_into_a_text_area_using_an_id_css_selector()
//    {
//        $this->visit('/login')
//            ->type('Some test text', '#testTextArea');
//    }
//
//    /** @test */
//    public function it_can_type_text_into_a_text_area_using_a_name_attribute()
//    {
//        $this->visit('/login')
//            ->type('Some test text', 'test-text-area');
//    }
//
//    /** @test */
//    public function it_can_type_text_into_a_text_area_using_an_id_attribute()
//    {
//        $this->visit('/login')
//            ->type('Some test text', 'testTextArea');
//    }
//
//    /** @test */
//    public function it_can_press_a_given_button_using_the_button_text()
//    {
//        $this->visit('/login')
//            ->press('Login');
//    }
//
//    /** @test */
//    public function it_can_press_a_given_button_using_an_id_css_selector()
//    {
//        $this->visit('/login')
//            ->press('#submitButton');
//    }
//
//    /** @test */
//    public function it_can_press_a_given_button_using_an_name_attribute()
//    {
//        $this->visit('/login')
//            ->press('submit');
//    }
//
//    /** @test */
//    public function it_can_press_a_given_button_using_an_id_attribute()
//    {
//        $this->visit('/login')
//            ->press('submitButton');
//    }
//
//    /** @test */
//    public function it_can_click_a_given_element_using_the_element_text()
//    {
//        $this->visit('/login')
//            ->clickOnElement('Test Text Input');
//    }
//
//    /** @test */
//    public function it_can_click_a_given_element_using_an_id_css_selector()
//    {
//        $this->visit('/login')
//            ->clickOnElement('#testTextInputLabel');
//    }
//
//    /** @test */
//    public function it_can_click_a_given_element_using_an_id_attribute()
//    {
//        $this->visit('/login')
//            ->clickOnElement('testTextInputLabel');
//    }
}