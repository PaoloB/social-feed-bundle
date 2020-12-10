<?php

namespace Pdir\SocialFeedBundle\EventListener;

use Contao\CoreBundle\Exception\RedirectResponseException;
use Contao\DataContainer;
use Contao\Environment;
use Contao\Input;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\RouterInterface;


class SocialFeedListener
{
    const SESSION_KEY = 'social-feed-id';

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * ModuleListener constructor.
     */
    public function __construct(RouterInterface $router, SessionInterface $session)
    {
        $this->router = $router;
        $this->session = $session;
    }

    /**
     * On submit callback.
     */
    public function onSubmitCallback(DataContainer $dc)
    {
        if ('Instagram' === $dc->activeRecord->socialFeedType && $dc->activeRecord->psf_instagramAppId && Input::post('psf_instagramRequestToken')) {
            $this->requestAccessToken($dc->activeRecord->psf_instagramAppId);
        }

        if ('LinkedIn' === $dc->activeRecord->socialFeedType && $dc->activeRecord->psf_linkedinClientId && $dc->activeRecord->psf_linkedinClientSecret && Input::post('psf_linkedInRequestToken')) {
            $this->requestLinkedinAccessToken($dc->activeRecord->psf_linkedinClientId, $dc->activeRecord->psf_linkedinClientSecret);
        }
    }

    /**
     * On the request token save.
     *
     * @return null
     */
    public function onRequestTokenSave()
    {
        return null;
    }

    /**
     * Request the Instagram access token.
     *
     * @param string $clientId
     */
    private function requestAccessToken($clientId)
    {
        $this->session->set(self::SESSION_KEY, [
            'socialFeedId' => Input::get('id'),
            'backUrl' => Environment::get('uri'),
        ]);

        $this->session->save();

        $data = [
            'app_id' => $clientId,
            'redirect_uri' => $this->router->generate('instagram_auth', [], RouterInterface::ABSOLUTE_URL),
            'response_type' => 'code',
            'scope' => 'user_profile,user_media',
        ];

        throw new RedirectResponseException('https://api.instagram.com/oauth/authorize/?'.http_build_query($data));
    }

    /**
     * Request the LinkedIn access token.
     *
     * @param string $clientId
     * @param string $clientSecret
     */
    private function requestLinkedinAccessToken($clientId, $clientSecret)
    {
        $this->session->set(self::SESSION_KEY, [
            'socialFeedId' => Input::get('id'),
            'backUrl' => Environment::get('uri'),
            'clientId' => $clientId,
            'clientSecret' => $clientSecret
        ]);

        $this->session->save();

        $data = [
            'response_type' => 'code',
            'client_id' => $clientId,
            'redirect_uri' => $this->router->generate('auth_linkedin', [], RouterInterface::ABSOLUTE_URL),
            'scope' => 'r_liteprofile'
        ];

        throw new RedirectResponseException('https://www.linkedin.com/oauth/v2/authorization?'.http_build_query($data));
    }
}
