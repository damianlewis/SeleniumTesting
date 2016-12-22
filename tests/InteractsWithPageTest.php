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
    public function it_can_assert_that_the_visited_page_url_matches_the_given_uri()
    {
        $this->visit('/login')
            ->seePageIs('login');
    }

    /** @test */
    public function it_can_assert_that_the_home_page_url_matches_the_given_uri()
    {
        $this->visit('/')
            ->seePageIs('/');
    }

    /** @test */
    public function it_can_assert_that_a_given_string_is_seen_on_the_page()
    {
        $this->visit('/login')
            ->see('Login');
    }

    /** @test */
    public function it_can_assert_that_a_given_string_is_not_seen_on_the_page()
    {
        $this->visit('/login')
            ->dontSee('qwerty123345');
    }

    /** @test */
    public function it_can_assert_that_a_given_element_is_seen_on_the_page_using_an_id_css_selector()
    {
        $this->visit('/login')
            ->seeElement('#app');
    }

    /** @test */
    public function it_can_assert_that_a_given_element_is_not_seen_on_the_page_using_an_id_css_selector()
    {
        $this->visit('/login')
            ->dontSeeElement('#qwerty123345');
    }

    /** @test */
    public function it_can_assert_that_a_given_element_is_seen_on_the_page_using_a_class_css_selector()
    {
        $this->visit('/login')
            ->seeElement('.panel-heading');
    }

    /** @test */
    public function it_can_assert_that_a_given_element_is_not_seen_on_the_page_using_a_class_css_selector()
    {
        $this->visit('/login')
            ->dontSeeElement('.qwerty123345');
    }

    /** @test */
    public function it_can_assert_that_a_given_element_is_seen_on_the_page_using_an_id_attribute()
    {
        $this->visit('/login')
            ->seeElement('app');
    }

    /** @test */
    public function it_can_assert_that_a_given_element_is_seen_on_the_page_using_a_name_attribute()
    {
        $this->visit('/login')
            ->seeElement('remember');
    }

    /** @test */
    public function it_can_assert_that_a_given_element_is_seen_on_the_page_using_a_class_attribute()
    {
        $this->visit('/login')
            ->seeElement('panel-heading');
    }

    /** @test */
    public function it_can_assert_that_a_given_element_is_seen_on_the_page_using_a_tag_name()
    {
        $this->visit('/login')
            ->seeElement('form');
    }

    /** @test */
    public function it_can_assert_that_a_given_element_is_not_seen_on_the_page_using_an_id_name_class_attribute_or_tag_name()
    {
        $this->visit('/login')
            ->dontSeeElement('qwerty123345');
    }

    /** @test */
    public function it_can_assert_that_a_given_element_contains_a_given_text_value_using_an_id_css_selector()
    {
        $this->visit('/login')
            ->seeInElement('#app-navbar-collapse', 'Register');
    }

    /** @test */
    public function it_can_assert_that_a_given_element_does_not_contain_a_given_text_value_using_an_id_css_selector()
    {
        $this->visit('/login')
            ->dontSeeInElement('#app-navbar-collapse', 'Laravel');
    }

    /** @test */
    public function it_can_assert_that_a_given_element_contains_a_given_text_value_using_a_class_css_selector()
    {
        $this->visit('/login')
            ->seeInElement('.panel-heading', 'Login');
    }

    /** @test */
    public function it_can_assert_that_a_given_element_does_not_contain_a_given_text_value_using_a_class_css_selector()
    {
        $this->visit('/login')
            ->dontSeeInElement('.panel-heading', 'Password');
    }

    /** @test */
    public function it_can_assert_that_a_given_element_contains_a_given_text_value_using_an_id_attribute()
    {
        $this->visit('/login')
            ->seeInElement('app-navbar-collapse', 'Register');
    }

    /** @test */
    public function it_can_assert_that_a_given_element_does_not_contain_a_given_text_value_using_an_id_attribute()
    {
        $this->visit('/login')
            ->dontSeeInElement('app-navbar-collapse', 'Laravel');
    }

    /** @test */
    public function it_can_assert_that_a_given_element_contains_a_given_text_value_using_a_class_attribute()
    {
        $this->visit('/login')
            ->seeInElement('panel-heading', 'Login');
    }

    /** @test */
    public function it_can_assert_that_a_given_element_does_not_contain_a_given_text_value_using_a_class_attribute()
    {
        $this->visit('/login')
            ->dontSeeInElement('panel-heading', 'Password');
    }

    /** @test */
    public function it_can_assert_that_a_given_element_contains_a_given_text_value_using_a_tag_name()
    {
        $this->visit('/login')
            ->seeInElement('p', 'Lorem ipsum dolor sit amet');
    }

    /** @test */
    public function it_can_assert_that_a_given_element_does_not_contain_a_given_text_value_using_a_tag_name()
    {
        $this->visit('/login')
            ->dontSeeInElement('p', 'qwerty12345');
    }

    /** @test */
    public function it_can_click_a_given_link_using_the_link_text()
    {
        $this->visit('/login')
            ->click('Register');
    }

    /** @test */
    public function it_can_click_a_given_link_using_an_id_css_selector()
    {
        $this->visit('/login')
            ->click('#registerLinkId');
    }

    /** @test */
    public function it_can_click_a_given_link_using_an_id_attribute()
    {
        $this->visit('/login')
            ->click('registerLinkId');
    }

    /** @test */
    public function it_can_type_text_into_an_input_using_an_id_css_selector()
    {
        $this->visit('/login')
            ->type('Some test text', '#testTextInput');
    }

    /** @test */
    public function it_can_type_text_into_an_input_using_a_name_attribute()
    {
        $this->visit('/login')
            ->type('Some test text', 'test-text-input');
    }

    /** @test */
    public function it_can_type_text_into_an_input_using_an_id_attribute()
    {
        $this->visit('/login')
            ->type('Some test text', 'testTextInput');
    }

    /** @test */
    public function it_can_type_text_into_a_text_area_using_an_id_css_selector()
    {
        $this->visit('/login')
            ->type('Some test text', '#testTextArea');
    }

    /** @test */
    public function it_can_type_text_into_a_text_area_using_a_name_attribute()
    {
        $this->visit('/login')
            ->type('Some test text', 'test-text-area');
    }

    /** @test */
    public function it_can_type_text_into_a_text_area_using_an_id_attribute()
    {
        $this->visit('/login')
            ->type('Some test text', 'testTextArea');
    }

    /** @test */
    public function it_can_press_a_given_button_using_the_button_text()
    {
        $this->visit('/login')
            ->press('Login');
    }

    /** @test */
    public function it_can_press_a_given_button_using_an_id_css_selector()
    {
        $this->visit('/login')
            ->press('#submitButton');
    }

    /** @test */
    public function it_can_press_a_given_button_using_an_name_attribute()
    {
        $this->visit('/login')
            ->press('submit');
    }

    /** @test */
    public function it_can_press_a_given_button_using_an_id_attribute()
    {
        $this->visit('/login')
            ->press('submitButton');
    }
}