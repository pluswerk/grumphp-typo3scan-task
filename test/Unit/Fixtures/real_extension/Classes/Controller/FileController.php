<?php

namespace Pluswerk\MvtecProduct\Controller;

use Aws\CloudFront\UrlSigner;
use Pluswerk\MvtecProduct\Domain\Repository\DownloadRepository;
use Pluswerk\MvtecProduct\Service\Security;
use TYPO3\CMS\Core\Http\ImmediateResponseException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Domain\Repository\FrontendUserGroupRepository;
use TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use TYPO3\CMS\Frontend\Controller\ErrorController;
use TYPO3\CMS\Frontend\Page\PageAccessFailureReasons;

class FileController extends ActionController
{

    /**
     * @var FrontendUserRepository
     */
    protected $frontendUserRepository;

    /**
     * @var FrontendUserGroupRepository
     */
    protected $frontendUserGroupRepository;

    /**
     * @var DownloadRepository
     */
    protected $downloadRepository;

    /**
     * @var PersistenceManager
     */
    protected $persistenceManager;

    /**
     * @var Security
     */
    protected $security;

    /**
     * @var string
     */
    protected $accessKeyID;

    /**
     * @var string
     */
    protected $privateKeyFile;

    /**
     * @var ErrorController
     */
    protected ErrorController $errorController;

    /**
     * Set extension configuration
     * @param FrontendUserRepository $frontendUserRepository
     * @param FrontendUserGroupRepository $frontendUserGroupRepository
     * @param DownloadRepository $downloadRepository
     * @param PersistenceManager $persistenceManager
     * @param Security $security
     *
     * @param ErrorController $errorController
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public function __construct(
        FrontendUserRepository $frontendUserRepository,
        FrontendUserGroupRepository $frontendUserGroupRepository,
        DownloadRepository $downloadRepository,
        PersistenceManager $persistenceManager,
        Security $security,
        ErrorController $errorController
    ) {
        $this->frontendUserRepository = $frontendUserRepository;
        $this->frontendUserGroupRepository = $frontendUserGroupRepository;
        $this->downloadRepository = $downloadRepository;
        $this->persistenceManager = $persistenceManager;
        $this->security = $security;
        $this->errorController = $errorController;

        $extConf = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
            \TYPO3\CMS\Core\Configuration\ExtensionConfiguration::class
        )->get(GeneralUtility::camelCaseToLowerCaseUnderscored('mvtec_product'));

        if (isset($extConf['cloudFrontAccessKeyIDFile']) && file_exists($extConf['cloudFrontAccessKeyIDFile'])) {
            /** @var string $fileContents */
            $fileContents = file_get_contents($extConf['cloudFrontAccessKeyIDFile']);
            $this->accessKeyID = $fileContents;
        }

        if (isset($extConf['cloudFrontPrivateKeyFile']) && is_string($extConf['cloudFrontPrivateKeyFile'])) {
            $this->privateKeyFile = $extConf['cloudFrontPrivateKeyFile'];
        }

        if ($this->accessKeyID === null || $this->privateKeyFile === null) {
            throw new \Exception('Missing / wrong extension configuration.');
        }
    }

    /**
     * Sign a URL using the AWS SDK
     *
     * Please note that this only works for CloudFront URL's
     * Otherwise an exception gets triggered
     *
     * @param \Pluswerk\MvtecProduct\Domain\Model\Download $download
     * @return string
     */
    public function dispatchAction(\Pluswerk\MvtecProduct\Domain\Model\Download $download)
    {
        $isAuthenticated = $this->security->isAuthenticated($this->settings['allowedUserGroups']);

        if ($isAuthenticated === true) {
            try {
                /** @var UrlSigner $urlSigner */
                $urlSigner = new UrlSigner(
                    $this->accessKeyID,
                    $this->privateKeyFile
                );

                /** @var string $downloadPublicUrl */
                $downloadPublicUrl = $download->getDownloadFile()->getOriginalResource()->getPublicUrl();
                $signedUrl = $urlSigner->getSignedUrl(
                    $downloadPublicUrl,
                    time() + 5
                );

                // Check if is an allowed IP to count
                if (
                    GeneralUtility::cmpIP(
                        GeneralUtility::getIndpEnv('REMOTE_ADDR'),
                        $this->settings['ignoredIpForDownloadCount']
                    ) === false
                ) {
                    // Increment download counter
                    $download->incrementCounter();

                    $this->downloadRepository->add($download);

                    $this->persistenceManager->persistAll();
                }

                $this->redirectToUri($signedUrl);
            } catch (\Exception $e) {
                $logger = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Log\LogManager::class)->getLogger(__CLASS__);
                $logger->log(\TYPO3\CMS\Core\Log\LogLevel::ERROR, $e->__toString(), ['ext' => 'mvtec_product']);

                $this->redirectToUri(
                    $this->controllerContext->getUriBuilder()->reset()->setCreateAbsoluteUri(true)
                    ->setTargetPageUid($this->settings['downloadNotAvailablePid'])
                    ->buildFrontendUri()
                );
            }
        }

        // User is not logged in.
        // Create 403 response with ErrorController.
        $response = $this->errorController->accessDeniedAction(
            $GLOBALS['TYPO3_REQUEST'],
            'Not authorized',
            ['code' => PageAccessFailureReasons::ACCESS_DENIED_GENERAL]
        );

        // Return response created from ErrorController.
        throw new ImmediateResponseException($response);
    }
}
