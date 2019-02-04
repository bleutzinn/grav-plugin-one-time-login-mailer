<?php
namespace Grav\Plugin;

use Grav\Common\Grav;
use Grav\Common\Plugin;
use Grav\Common\Utils;
use Grav\Common\Config\Config;
use Grav\Common\File\CompiledYamlFile;
use Grav\Common\User\User;
use Grav\Console\ConsoleCommand;
use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use RocketTheme\Toolbox\Event\Event;

/**
 * Class OneTimeLoginMailerPlugin
 * @package Grav\Plugin
 */
class OneTimeLoginMailerPlugin extends Plugin
{
    /**
     * @return array
     *
     * The getSubscribedEvents() gives the core a list of events
     *     that the plugin wants to listen to. The key of each
     *     array section is the event that the plugin listens to
     *     and the value (in the form of an array) contains the
     *     callable (or function) as well as the priority. The
     *     higher the number the higher the priority.
     */
    public static function getSubscribedEvents()
    {
        return [
            'onPluginsInitialized' => ['onPluginsInitialized', 0]
        ];
    }

    /**
     * Initialize the plugin
     */
    public function onPluginsInitialized()
    {
        // Don't proceed if we are in the admin plugin
        if ($this->isAdmin()) {
            return;
        }

        // Enable the main event we are interested in
        $this->enable([
            //'getQueryVar' => ['getQueryVar', 0],
            'onFormProcessed' => ['onFormProcessed', 0]
        ]);
    }

    /**
     * Helper function to get the URL Query string
     * for instance like this in a Gravform field:
     *   data-default@:
     *      - 'Grav\Plugin\OneTimeLoginMailerPlugin::getQueryVar'
     *      - user
     */
    public static function getQueryVar($var)
    {
        return Grav::instance()['uri']->query($var);
    }

    /**
     * Handle form action
     *
     * @param $event
     *
     */
    public function onFormProcessed(Event $event)
    {
        $form = $event['form'];
        $action = $event['action'];
        $params = $event['params'];
        // Check whether to act upon form processing
        if ($action == 'otp_request') {
            // Get all form field values
            $form_data = $form->value()->toArray();
            $username = $form_data['username'];
            $user = User::load($username);
            //dump($user->email);exit;
            if ($user->exists()) {
                $now = time();
                $validFor = 15; // minutes
                $dateFormat = 'd-m-Y H:i';
                $startValid = date($dateFormat, $now);
                $endValid = date($dateFormat, $now + ($validFor * 60));

                $from = 'me@example.org';
                $to = $user->email;
                $subject = 'Login link voor de Staat van Groningen';
                $otpUrl = $this->createNonce($username);
                $content  = 'Gebruik onderstaande link om in te loggen op de Staat van Groningen.';
                $content .= '<br><br>';
                $content .= 'Let op: deze link is ' . $validFor . ' minuten';
                $content .= ' en kan slechts één keer worden gebruikt.';
                $content .= '<br><br>';
                $content .= '<a href="' . $otpUrl . '">' . $otpUrl . '</a>';
                
                $message = $this->grav['Email']
                    ->message($subject, $content, 'text/html')
                    ->setFrom($from)
                    ->setTo($to);
                $sent = $this->grav['Email']->send($message);
            }
        }
    }

    /**
     * @return int|null|void
     */
    protected function createNonce($username)
    {
        // Start session to generate a new token value each time.
        session_start();
        
        $config = Grav::instance()['config'];
        $param_sep = $config['system']['param_sep'];

        $token  = md5(uniqid(mt_rand(), true));
        $token_expire = time() + 900; // 15 minutes
        $nonce = Utils::getNonce('admin-form', false);

        // Load user object.
        $user = !empty($username) ? User::load($username) : null;
        
        // Set OTL nonce/ expiration.
        $user->otl_nonce = $nonce;
        $user->otl_nonce_expire = $token_expire;
        
        // Save user object with otl values.
        $user->save();
        
        // Construct URL
        $base_uri = $config->get('plugins.one-time-login.base_otl_utl') . $config->get('plugins.one-time-login.otl_route') . '/';
        $url = $base_uri . 'user' . $param_sep . $username . '/otl_nonce' . $param_sep . $nonce;
        
        return $url;
    }

}
