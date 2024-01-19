<?php
declare(strict_types=1);

namespace MageINIC\Translation\Model\Config\Source;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Locale\Bundle\LanguageBundle;
use Magento\Framework\Locale\Bundle\RegionBundle;
use Magento\Framework\Locale\ConfigInterface;
use Magento\Framework\Locale\ResolverInterface;
use Magento\Framework\Option\ArrayInterface;
use Magento\Store\Model\StoreManagerInterface;

class LocaleOption implements ArrayInterface
{
    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;

    /**
     * @var ScopeConfigInterface
     */
    private ScopeConfigInterface $scopeConfig;

    /**
     * @var ResolverInterface
     */
    protected ResolverInterface $localeResolver;

    /**
     * @var ConfigInterface
     */
    protected ConfigInterface $config;

    /**
     * Locale Option Construct
     *
     * @param StoreManagerInterface $storeManager
     * @param ScopeConfigInterface $scopeConfig
     * @param ResolverInterface $localeResolver
     * @param ConfigInterface $config
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig,
        ResolverInterface $localeResolver,
        ConfigInterface $config
    ) {
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->localeResolver =  $localeResolver;
        $this->config = $config;
    }

    /**
     * Sort option array.
     *
     * @param array $option
     * @return array
     */
    protected function sortOptionArray(array $option): array
    {
        $data = [];
        foreach ($option as $item) {
            $data[$item['value']] = $item['label'];
        }
        asort($data);
        $option = [];
        foreach ($data as $key => $label) {
            $option[] = ['value' => $key, 'label' => $label];
        }
        return $option;
    }

    /**
     * @return array
     */
    public function toOptionArray(): array
    {
        $locales = [];
        foreach ($this->storeManager->getStores() as $store) {
            $locale = $this->scopeConfig->getValue('general/locale/code', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store->getStoreId());
            if (!in_array($locale, $locales)) {
                $locales[] = $locale;
            }
        }
        $currentLocale = $this->localeResolver->getLocale();
        $languages = (new LanguageBundle())->get($currentLocale)['Languages'];
        $countries = (new RegionBundle())->get($currentLocale)['Countries'];
        $options = [];
        $allowedLocales = $this->config->getAllowedLocales();
        foreach ($locales as $locale) {
            if (!in_array($locale, $allowedLocales)) {
                continue;
            }
            $language = \Locale::getPrimaryLanguage($locale);
            $country = \Locale::getRegion($locale);
            $script = \Locale::getScript($locale);
            $scriptTranslated = '';
            if ($script !== '') {
                $script = \Locale::getDisplayScript($locale) . ', ';
                $scriptTranslated = \Locale::getDisplayScript($locale, $locale) . ', ';
            }

            $label = $languages[$language]
                . ' (' . $script . $countries[$country] . ')';

            $options[] = ['value' => $locale, 'label' => $label];
        }
        return $this->sortOptionArray($options);
    }
}
