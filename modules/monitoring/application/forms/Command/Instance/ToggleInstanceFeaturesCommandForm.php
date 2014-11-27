<?php
// {{{ICINGA_LICENSE_HEADER}}}
// {{{ICINGA_LICENSE_HEADER}}}

namespace Icinga\Module\Monitoring\Forms\Command\Instance;

use Icinga\Module\Monitoring\Command\Instance\ToggleInstanceFeatureCommand;
use Icinga\Module\Monitoring\Forms\Command\CommandForm;
use Icinga\Web\Notification;

/**
 * Form for enabling or disabling features of Icinga objects, i.e. hosts or services
 */
class ToggleInstanceFeaturesCommandForm extends CommandForm
{
    /**
     * Instance status
     *
     * @var object
     */
    protected $status;

    /**
     * (non-PHPDoc)
     * @see \Zend_Form::init() For the method documentation.
     */
    public function init()
    {
        $this->setAttrib('class', 'inline instance-features');
    }

    /**
     * Set the instance status
     *
     * @param   object $status
     *
     * @return  $this
     */
    public function setStatus($status)
    {
        $this->status = (object) $status;
        return $this;
    }

    /**
     * Get the instance status
     *
     * @return object
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * (non-PHPDoc)
     * @see \Icinga\Web\Form::createElements() For the method documentation.
     */
    public function createElements(array $formData = array())
    {
        if ((bool) $this->status->notifications_enabled) {
            $notificationDescription = sprintf(
                '<a title="%s" href="%s" data-base-target="_next">%s</a>',
                mt('monitoring', 'Disable notifications for a specific time on a program-wide basis'),
                $this->getView()->href('monitoring/process/disable-notifications'),
                mt('monitoring', 'Disable temporarily')
            );
        } elseif ($this->status->disable_notif_expire_time) {
            $notificationDescription = sprintf(
                mt('monitoring', 'Notifications will be re-enabled %s'),
                $this->getView()->dateTimeRenderer(
                    $this->status->disable_notif_expire_time,
                    true
                )->render(
                    mt('monitoring', 'on <strong>%s</strong>', 'datetime'),
                    mt('monitoring', 'at <strong>%s</strong>', 'time'),
                    mt('monitoring', 'in <strong>%s</strong>', 'timespan')
                )
            );
        } else {
            $notificationDescription = null;
        }
        $this->addElements(array(
            array(
                'checkbox',
                ToggleInstanceFeatureCommand::FEATURE_ACTIVE_HOST_CHECKS,
                array(
                    'label'         =>  mt('monitoring', 'Active Host Checks Being Executed'),
                    'autosubmit'    => true
                )
            ),
            array(
                'checkbox',
                ToggleInstanceFeatureCommand::FEATURE_ACTIVE_SERVICE_CHECKS,
                array(
                    'label'         =>  mt('monitoring', 'Active Service Checks Being Executed'),
                    'autosubmit'    => true
                )
            ),
            array(
                'checkbox',
                ToggleInstanceFeatureCommand::FEATURE_EVENT_HANDLERS,
                array(
                    'label'         => mt('monitoring', 'Event Handlers Enabled'),
                    'autosubmit'    => true
                )
            ),
            array(
                'checkbox',
                ToggleInstanceFeatureCommand::FEATURE_FLAP_DETECTION,
                array(
                    'label'         => mt('monitoring', 'Flap Detection Enabled'),
                    'autosubmit'    => true
                )
            ),
            array(
                'checkbox',
                ToggleInstanceFeatureCommand::FEATURE_NOTIFICATIONS,
                array(
                    'label'         => mt('monitoring', 'Notifications Enabled'),
                    'autosubmit'    => true,
                    'description'   => $notificationDescription,
                    'decorators'    => array(
                        'ViewHelper',
                        'Errors',
                        array(
                            'Description',
                            array('tag' => 'span', 'class' => 'description', 'escape' => false)
                        ),
                        'Label',
                        array('HtmlTag', array('tag' => 'div'))
                    )
                )
            ),
            array(
                'checkbox',
                ToggleInstanceFeatureCommand::FEATURE_HOST_OBSESSING,
                array(
                    'label'         => mt('monitoring', 'Obsessing Over Hosts'),
                    'autosubmit'    => true
                )
            ),
            array(
                'checkbox',
                ToggleInstanceFeatureCommand::FEATURE_SERVICE_OBSESSING,
                array(
                    'label'         => mt('monitoring', 'Obsessing Over Services'),
                    'autosubmit'    => true
                )
            ),
            array(
                'checkbox',
                ToggleInstanceFeatureCommand::FEATURE_PASSIVE_HOST_CHECKS,
                array(
                    'label'         =>  mt('monitoring', 'Passive Host Checks Being Accepted'),
                    'autosubmit'    => true
                )
            ),
            array(
                'checkbox',
                ToggleInstanceFeatureCommand::FEATURE_PASSIVE_SERVICE_CHECKS,
                array(
                    'label'         =>  mt('monitoring', 'Passive Service Checks Being Accepted'),
                    'autosubmit'    => true
                )
            ),
            array(
                'checkbox',
                ToggleInstanceFeatureCommand::FEATURE_PERFORMANCE_DATA,
                array(
                    'label'         =>  mt('monitoring', 'Performance Data Being Processed'),
                    'autosubmit'    => true
                )
            )
        ));
        return $this;
    }

    /**
     * Load feature status
     *
     * @param   object $instanceStatus
     *
     * @return  $this
     */
    public function load($instanceStatus)
    {
        $this->create();
        foreach ($this->getValues() as $feature => $enabled) {
            $this->getElement($feature)->setChecked($instanceStatus->{$feature});
        }
        return $this;
    }

    /**
     * (non-PHPDoc)
     * @see \Icinga\Web\Form::onSuccess() For the method documentation.
     */
    public function onSuccess()
    {
        foreach ($this->getValues() as $feature => $enabled) {
            $toggleFeature = new ToggleInstanceFeatureCommand();
            $toggleFeature
                ->setFeature($feature)
                ->setEnabled($enabled);
            $this->getTransport($this->request)->send($toggleFeature);
        }
        Notification::success(mt('monitoring', 'Toggling feature..'));
        return true;
    }
}
