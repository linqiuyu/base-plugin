# wordpress插件开发样板

## 创建模块
如翻译模块 /includes/I8n，创建I18n类

## 添加服务提供者如 /includes/I18n/I18nServiceProvider
绑定实例
```
class I18nServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['i18n'] = function () {
            return new I18n();
        };
    }
}
```
在 /includes/Application.php的providers中添加服务提供者:
```
protected array $providers = [
    I18nServiceProvider::class,
    ......
];
```

## 在processors中统一注册钩子
在 /processors/Actions.php中将翻译添加到wordpress
```
public function process($app)
    {
        ......
        add_action('template_redirect', [$this, 'template_actions']);
    }
```