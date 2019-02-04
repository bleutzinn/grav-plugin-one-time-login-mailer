# One Time Login Mailer Plugin

The **One Time Login Mailer** Plugin is for [Grav CMS](http://github.com/getgrav/grav). 

It sends a One Time Login Link to a known user by email. The actual logging in is handled by the One Time Login Plugin.

## Installation

Installing the One Time Login Mailer plugin can be done in one of two ways. The GPM (Grav Package Manager) installation method enables you to quickly and easily install the plugin with a simple terminal command, while the manual method enables you to do so via a zip file.

### GPM Installation (Preferred)

The simplest way to install this plugin is via the [Grav Package Manager (GPM)](http://learn.getgrav.org/advanced/grav-gpm) through your system's terminal (also called the command line).  From the root of your Grav install type:

    bin/gpm install one-time-login-mailer

This will install the One Time Login Mailer plugin into your `/user/plugins` directory within Grav. Its files can be found under `/your/site/grav/user/plugins/one-time-login-mailer`.

### Manual Installation

To install this plugin, just download the zip version of this repository and unzip it under `/your/site/grav/user/plugins`. Then, rename the folder to `one-time-login-mailer`. You can find these files on [GitHub](https://github.com/bleutzinn/grav-plugin-one-time-login-mailer) or via [GetGrav.org](http://getgrav.org/downloads/plugins#extras).

You should now have all the plugin files under

    /your/site/grav/user/plugins/one-time-login-mailer

> NOTE: This plugin is a modular component for Grav which requires [Grav](http://github.com/getgrav/grav) and the [Error](https://github.com/getgrav/grav-plugin-error) and [Problems](https://github.com/getgrav/grav-plugin-problems) to operate.

### Admin Plugin

If you use the admin plugin, you can install directly through the admin plugin by browsing the `Plugins` tab and clicking on the `Add` button.

## Requirements

The pluging requires the One Time Login Plugin and the Email Plugin.

## Configuration

Before configuring this plugin, you should copy the `user/plugins/one-time-login-mailer/one-time-login-mailer.yaml` to `user/config/plugins/one-time-login-mailer.yaml` and only edit that copy.

Here is the default configuration and an explanation of available options:

```yaml
enabled: true
```

Note that if you use the admin plugin, a file with your configuration, and named one-time-login-mailer.yaml will be saved in the `user/config/plugins/` folder once the configuration is saved in the admin.

## Usage

Currently the email sender address, the subject and email message text are hard coded in the `one-time-login-mailer.php` file.

The plugin relies on a properly configured Email Plugin to send out the actual email.

