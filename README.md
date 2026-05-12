# PHP-JsMaker
No framework, PHP changes HTML direactly！

# 老子不会英文，我就用中文写了！

本项目就一个文件JsMaker.php，用于PHP生成对前端HTML更改的JS。

## 用法

### 1. 选择器语法

独特的 `selector` 语法，支持跨窗口/框架操作：

作用域说白了就是前缀

| 语法示例 | 对应 JS 作用域 | 说明 |
| --- | --- | --- |
| `!p` | `parent` | 当前窗口的父级 |
| `!p2` | `parent.parent` | 上两级窗口 |
| `!p3` | `parent.parent.parent` | 上三级窗口（最高支持到 `!p5`） |
| `!t` | `top` | 顶层窗口 |
| `!<parent.parent.parent.parent.parent.parent>` | `parent.parent.parent.parent.parent.parent` | 用于乱七八糟的作用域 |
| `#id` (普通字符串) | `window` | 默认在当前作用域下进行 CSS 查询 |

### 2. 基本属性修改与方法调用

#### `JsMaker::change($selector, $attr, $val)`

修改指定作用域下的全局变量或属性。

```php
// 生成: parent.isUpdated=true;
echo JsMaker::change('!p', 'isUpdated', true);

// 生成: top.theme="dark";
echo JsMaker::change('!t', 'theme', 'dark');

```

#### `JsMaker::call($selector, $func, ...$args)`

调用指定作用域下的函数。

```php
// 生成: parent.parent.alert("操作成功");
echo JsMaker::call('!p2', 'alert', '操作成功');

// 生成: myFrame.initData({"id":1});
echo JsMaker::call('!<myFrame>', 'initData', ['id' => 1]);

```

---

### 3. DOM 元素操作 (E-Series)

前缀为 `e` 的方法会自动将剩余的选择器部分包装进 `document.querySelector()`。

#### `JsMaker::eChange($selector, $attr, $val)`

修改 DOM 元素的属性或样式。

```php
// 生成: parent.document.querySelector("#msg").innerText="Hello";
echo JsMaker::eChange('!p#msg', 'innerText', 'Hello');

// 生成: document.querySelector(".btn").disabled=true;
echo JsMaker::eChange('.btn', 'disabled', true);

```

#### `JsMaker::eCall($selector, $func, ...$args)`

调用 DOM 元素的方法。

```php
// 生成: document.querySelector("form").reset();
echo JsMaker::eCall('form', 'reset');

// 生成: top.document.querySelector("#video").play();
echo JsMaker::eCall('!t#video', 'play');

```

---

### 4. 快捷工具方法

#### 页面跳转与刷新

```php
// 刷新顶层窗口 (生成: top.location.reload();)
echo JsMaker::reload();

// 刷新当前窗口 (生成: location.reload();)
echo JsMaker::reload('');

// 跳转页面 (生成: top.location.href="login.php";)
echo JsMaker::redirect('login.php');

```

#### 延迟执行 (`setTimeout`)

```php
// 3秒后刷新
$js = JsMaker::reload();
echo JsMaker::setTimeout($js, 3);
// 生成: setTimeout(() => {top.location.reload();},3000);

```

**注意：** 生成的 JS 字符串应确保在安全的上下文中运行，并注意转义可能存在的注入风险。
