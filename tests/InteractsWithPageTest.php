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
    public function it_can_assert_that_an_element_is_seen_on_the_page_with_the_given_css_id_selector()
    {
        $this->visit('/login')
            ->seeElement('#app');
    }

    /** @test */
    public function it_can_assert_that_an_element_is_seen_on_the_page_with_the_given_css_class_selector()
    {
        $this->visit('/login')
            ->seeElement('.panel-heading');
    }

    /** @test */
    public function it_can_assert_that_an_element_is_seen_on_the_page_with_the_given_id_attribute()
    {
        $this->visit('/login')
            ->seeElement('app');
    }

    /** @test */
    public function it_can_assert_that_an_element_is_seen_on_the_page_with_the_given_name_attribute()
    {
        $this->visit('/login')
            ->seeElement('remember');
    }

    /** @test */
    public function it_can_assert_that_an_element_is_seen_on_the_page_with_the_given_class_attribute()
    {
        $this->visit('/login')
            ->seeElement('panel-heading');
    }

    /** @test */
    public function it_can_assert_that_an_element_is_seen_on_the_page_with_the_given_tag_name()
    {
        $this->visit('/login')
            ->seeElement('form');
    }

    /** @test */
    public function it_can_assert_that_an_element_is_not_seen_on_the_page_with_the_given_css_id_selector()
    {
        $this->visit('/login')
            ->dontSeeElement('#nothingToSee');
    }

    /** @test */
    public function it_can_assert_that_an_element_is_not_seen_on_the_page_with_the_given_css_class_selector()
    {
        $this->visit('/login')
            ->dontSeeElement('.nothing-to-see');
    }

    /** @test */
    public function it_can_assert_that_an_element_is_not_seen_on_the_page_with_the_given_id_name_class_attribute_or_tag_name()
    {
        $this->visit('/login')
            ->dontSeeElement('nothing-to-see');
    }

    /** @test */
    public function it_can_assert_that_the_given_text_is_seen_on_the_page()
    {
        $this->visit('/login')
            ->seeText('E-Mail Address');
    }

    /** @test */
    public function it_can_assert_that_the_given_text_is_not_seen_on_the_page()
    {
        $this->visit('/login')
            ->dontSeeText('Nothing to see here');
    }

    /** @test */
    public function it_can_assert_that_an_element_is_seen_on_the_page_with_the_given_css_id_selector_and_containing_the_given_text()
    {
        $this->visit('/login')
            ->seeInElement('#app-navbar-collapse', 'Register');
    }

    /** @test */
    public function it_can_assert_that_an_element_is_seen_on_the_page_with_the_given_css_class_selector_and_containing_the_given_text()
    {
        $this->visit('/login')
            ->seeInElement('.panel-heading', 'Login');
    }

    /** @test */
    public function it_can_assert_that_an_element_is_seen_on_the_page_with_the_given_id_attribute_and_containing_the_given_text()
    {
        $this->visit('/login')
            ->seeInElement('app-navbar-collapse', 'Register');
    }

    /** @test */
    public function it_can_assert_that_an_element_is_seen_on_the_page_with_the_given_class_attribute_and_containing_the_given_text()
    {
        $this->visit('/login')
            ->seeInElement('panel-heading', 'Login');
    }

    /** @test */
    public function it_can_assert_that_an_element_is_seen_on_the_page_with_the_given_tag_and_containing_the_given_text()
    {
        $this->visit('/login')
            ->seeInElement('p', 'Lorem ipsum dolor sit amet');
    }

    /** @test */
    public function it_can_assert_that_an_element_is_not_seen_on_the_page_with_the_given_css_id_selector_and_containing_the_given_text()
    {
        $this->visit('/login')
            ->dontSeeInElement('#app-navbar-collapse', 'Laravel');
    }

    /** @test */
    public function it_can_assert_that_an_element_is_not_seen_on_the_page_with_the_given_css_class_selector_and_containing_the_given_text()
    {
        $this->visit('/login')
            ->dontSeeInElement('.panel-heading', 'Password');
    }

    /** @test */
    public function it_can_assert_that_an_element_is_not_seen_on_the_page_with_the_given_id_attribute_and_containing_the_given_text()
    {
        $this->visit('/login')
            ->dontSeeInElement('app-navbar-collapse', 'Laravel');
    }

    /** @test */
    public function it_can_assert_that_an_element_is_not_seen_on_the_page_with_the_given_class_attribute_and_containing_the_given_text()
    {
        $this->visit('/login')
            ->dontSeeInElement('panel-heading', 'Password');
    }

    /** @test */
    public function it_can_assert_that_an_element_is_not_seen_on_the_page_with_the_given_tag_and_containing_the_given_text()
    {
        $this->visit('/login')
            ->dontSeeInElement('p', 'nothing to see here');
    }

    /** @test */
    public function it_can_assert_that_a_link_is_seen_on_the_page_with_the_given_link_text()
    {
        $this->visit('/login')
            ->seeLink('Visit Laravel');
    }

    /** @test */
    public function it_can_assert_that_a_link_is_seen_on_the_page_with_the_given_link_text_and_full_url()
    {
        $this->visit('/login')
            ->seeLink('Visit Laravel', 'https://laravel.com');
    }

    /** @test */
    public function it_can_assert_that_a_link_is_seen_on_the_page_with_the_given_link_text_and_full_url_with_trailing_slash()
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
    public function it_can_assert_that_a_link_is_seen_on_the_page_with_the_given_link_text_and_partial_url_with_trailing_slash()
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

//    /** @test */
//    public function it_can_click_a_given_link_using_the_link_text()
//    {
//        $this->visit('/login')
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