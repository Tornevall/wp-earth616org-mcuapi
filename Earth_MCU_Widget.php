<?php

use TorneLIB\Utils\Generic;

/**
 * Earth616 Widget Controller.
 */
class Earth_MCU_Widget extends WP_Widget
{
    /** @var Generic */
    private $generic;

    private $apiUrl = 'https://api.earth616.org/api/mcu';
    private $apiResponse = ['film' => [], 'tv' => []];
    private $requestLimit = 1;

    public function __construct()
    {
        $this->generic = new Generic();
        $this->setupGenerics();

        parent::__construct(
            'earth616_mcu_widget',
            'Earth616.ORG MCU Timeline Widget',
            [
                'description' => __('Earth616.ORG MCU Timeline Widget', 'emt_widget'),
            ]
        );
    }

    /**
     * @return $this
     */
    private function setupGenerics()
    {
        $this->generic->setTemplatePath(__DIR__ . '/templates');
        return $this;
    }

    /**
     * @param string $requestType
     * @return bool
     */
    private function getNextFromApi($requestType = 'any')
    {
        $return = false;

        $urls = [
            'film' => sprintf('%s/next?type=movie&limit=%d', $this->apiUrl, $this->requestLimit),
            'tv' => sprintf('%s/next?type=tv&limit=%d', $this->apiUrl, $this->requestLimit),
            'any' => sprintf('%s/next?limit=%s', $this->apiUrl, $this->requestLimit),
        ];

        if (isset($urls[$requestType])) {
            try {
                $requestResponse = wp_remote_request($urls[$requestType]);
                $this->apiResponse = isset($requestResponse['body']) ?
                    json_decode(
                        $requestResponse['body'],
                        true
                    ) : [];
                $return = true;
            } catch (Exception $e) {
                $this->apiResponse[$requestType] = $e->getMessage();
            }
        }

        return $return;
    }

    /**
     * @param array $args
     * @param array $instance
     * @throws Exception
     */
    public function widget($args, $instance)
    {
        $before_title = '';
        $after_title = '';
        $before_widget = '';
        $after_widget = '';

        if ($this->getNextFromApi()) {
            /** @noinspection NonSecureExtractUsageInspection */
            extract($args);

            $assignedView = [
                'before_widget' => $before_widget,
                'after_widget' => $after_widget,
                'before_title' => $before_title,
                'after_title' => $after_title,
                'title' => apply_filters('widget_title', $instance['title'] ?? ''),
                'apiResponse' => $this->apiResponse,
                'nextInfoTemplate' => $this->generic->getTemplate('nextTemplate', $this->apiResponse),
            ];

            echo $this->generic->getTemplate('display.phtml', $assignedView);
        }
    }

    /**
     * @param array $instance
     * @return string|void
     * @throws Exception
     */
    public function form($instance)
    {
        echo $this->generic->getTemplate('form.phtml');
    }

    /**
     * @param array $new_instance
     * @param array $old_instance
     * @return array
     */
    public function update($new_instance, $old_instance)
    {
        $instance = [];
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';

        return $instance;
    }
}
