# Note GPT

一個基於 Laravel 的筆記管理系統 API。

## 系統需求

- PHP >= 8.2
- MySQL >= 8.0
- Composer
- Laravel 12.x

## 安裝步驟

1. 克隆專案
```bash
git clone https://github.com/your-username/note-gpt.git
cd note-gpt
```

2. 安裝依賴以及環境設定
```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan db:seed
php artisan vendor:publish --force --tag=livewire:assets
php artisan filament:assets
php artisan filament:cache-components
```

4. 設定資料庫
在 `.env` 文件中配置資料庫連接：
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=note_gpt
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

5. 執行資料庫遷移
```bash
php artisan migrate
```

6. 設定 Sanctum（API 認證）
在 `.env` 文件中添加：
```env
SANCTUM_STATEFUL_DOMAINS=localhost:8000
SESSION_DOMAIN=localhost
```

## 專案結構

```
app/
├── Http/
│   ├── Controllers/
│   │   └── Api/
│   │       ├── AuthController.php      # 認證相關
│   │       ├── NoteController.php      # 筆記管理
│   │       └── NoteFolderController.php # 資料夾管理
│   ├── Requests/
│   │   └── NoteRequest.php            # 筆記請求驗證
│   └── Resources/
│       └── MemberResource.php         # 會員資源轉換
├── Models/
│   ├── Member.php                     # 會員模型
│   ├── Note.php                       # 筆記模型
│   └── NoteFolder.php                 # 資料夾模型
└── Providers/
    └── AuthServiceProvider.php        # 認證服務提供者

database/
└── migrations/                        # 資料庫遷移文件
```

## 功能特點

- 會員系統
  - 使用 Laravel Sanctum 進行 API 認證
  - Token 有效期為 1 小時
  - 支援 Token 刷新機制

- 資料夾管理
  - 支援多層級資料夾結構
  - 自動維護資料夾排序
  - 防止刪除非空資料夾

- 筆記管理
  - 支援資料夾內筆記
  - 支援未分類筆記
  - 自動按更新時間排序


## API 文檔

### 認證 API

#### 登入
```http
POST /api/login
Content-Type: application/json

{
    "email": "user@example.com",
    "password": "password"
}
```

回應：
```json
{
    "token": "your-api-token",
    "member": {
        "id": 1,
        "name": "User Name",
        "email": "user@example.com"
    },
    "expires_in": 3600
}
```

#### 刷新 Token
```http
POST /api/refresh
Authorization: Bearer your-api-token
```

回應：
```json
{
    "token": "new-api-token",
    "expires_in": 3600
}
```

#### 登出
```http
POST /api/logout
Authorization: Bearer your-api-token
```

### Flutter 實作指南

#### 1. 安裝必要套件
在 `pubspec.yaml` 中添加：
```yaml
dependencies:
  shared_preferences: ^2.2.0  # 用於儲存 token
  dio: ^5.3.0  # HTTP 客戶端
  flutter_secure_storage: ^8.0.0  # 安全儲存
```

#### 2. 建立認證服務
```dart
class AuthService {
  final Dio _dio = Dio();
  final FlutterSecureStorage _storage = FlutterSecureStorage();
  
  // 登入
  Future<void> login(String email, String password) async {
    try {
      final response = await _dio.post(
        'YOUR_API_URL/api/login',
        data: {
          'email': email,
          'password': password,
        },
      );
      
      final token = response.data['token'];
      final expiresIn = response.data['expires_in'];
      
      // 儲存 token 和過期時間
      await _storage.write(key: 'token', value: token);
      await _storage.write(
        key: 'token_expires_at',
        value: (DateTime.now().millisecondsSinceEpoch + (expiresIn * 1000)).toString(),
      );
      
      // 設定 Dio 的認證標頭
      _dio.options.headers['Authorization'] = 'Bearer $token';
    } catch (e) {
      throw Exception('登入失敗：$e');
    }
  }
  
  // 刷新 Token
  Future<void> refreshToken() async {
    try {
      final token = await _storage.read(key: 'token');
      final response = await _dio.post(
        'YOUR_API_URL/api/refresh',
        options: Options(
          headers: {'Authorization': 'Bearer $token'},
        ),
      );
      
      final newToken = response.data['token'];
      final expiresIn = response.data['expires_in'];
      
      // 更新儲存的 token
      await _storage.write(key: 'token', value: newToken);
      await _storage.write(
        key: 'token_expires_at',
        value: (DateTime.now().millisecondsSinceEpoch + (expiresIn * 1000)).toString(),
      );
      
      // 更新 Dio 的認證標頭
      _dio.options.headers['Authorization'] = 'Bearer $newToken';
    } catch (e) {
      throw Exception('Token 刷新失敗：$e');
    }
  }
  
  // 檢查 Token 是否過期
  Future<bool> isTokenExpired() async {
    final expiresAt = await _storage.read(key: 'token_expires_at');
    if (expiresAt == null) return true;
    
    final expiryTime = int.parse(expiresAt);
    return DateTime.now().millisecondsSinceEpoch >= expiryTime;
  }
  
  // 登出
  Future<void> logout() async {
    try {
      final token = await _storage.read(key: 'token');
      await _dio.post(
        'YOUR_API_URL/api/logout',
        options: Options(
          headers: {'Authorization': 'Bearer $token'},
        ),
      );
      
      // 清除儲存的 token
      await _storage.delete(key: 'token');
      await _storage.delete(key: 'token_expires_at');
      
      // 清除 Dio 的認證標頭
      _dio.options.headers.remove('Authorization');
    } catch (e) {
      throw Exception('登出失敗：$e');
    }
  }
}
```

#### 3. 使用 Dio 攔截器自動處理 Token 刷新
```dart
void setupDioInterceptors() {
  _dio.interceptors.add(
    InterceptorsWrapper(
      onRequest: (options, handler) async {
        // 檢查 token 是否過期
        if (await isTokenExpired()) {
          try {
            await refreshToken();
            // 更新請求的認證標頭
            final token = await _storage.read(key: 'token');
            options.headers['Authorization'] = 'Bearer $token';
          } catch (e) {
            // Token 刷新失敗，可能需要重新登入
            throw Exception('認證已過期，請重新登入');
          }
        }
        return handler.next(options);
      },
      onError: (error, handler) async {
        if (error.response?.statusCode == 401) {
          try {
            await refreshToken();
            // 重試原始請求
            final token = await _storage.read(key: 'token');
            final options = Options(
              method: error.requestOptions.method,
              headers: {
                ...error.requestOptions.headers,
                'Authorization': 'Bearer $token',
              },
            );
            final response = await _dio.request(
              error.requestOptions.path,
              options: options,
              data: error.requestOptions.data,
            );
            return handler.resolve(response);
          } catch (e) {
            // Token 刷新失敗，可能需要重新登入
            throw Exception('認證已過期，請重新登入');
          }
        }
        return handler.next(error);
      },
    ),
  );
}
```

### 資料夾 API

#### 獲取資料夾列表
```http
GET /api/folders
Authorization: Bearer your-api-token
```

回應：
```json
[
    {
        "id": 1,
        "name": "測試",
        "arrow_path": "測試",
        "sort_order": 0,
        "children": [
            {
                "id": 2,
                "name": "測試01",
                "arrow_path": "測試 -> 測試01",
                "sort_order": 0,
                "children": [
                    {
                        "id": 3,
                        "name": "測試1-1",
                        "arrow_path": "測試 -> 測試01 -> 測試1-1",
                        "sort_order": 0
                    }
                ]
            }
        ]
    }
]
```

#### 創建資料夾
```http
POST /api/folders
Content-Type: application/json
Authorization: Bearer your-api-token

{
    "name": "新資料夾",
    "parent_id": 0    // 如果是最上層資料夾，可以傳 0 或不傳
}
```

回應：
```json
{
    "message": "資料夾建立成功",
    "data": {
        "id": 4,
        "name": "新資料夾",
        "arrow_path": "新資料夾",
        "sort_order": 1
    }
}
```

#### 更新資料夾
```http
PUT /api/folders/{id}
Content-Type: application/json
Authorization: Bearer your-api-token

{
    "name": "更新的資料夾名稱"
}
```

回應：
```json
{
    "message": "資料夾更新成功",
    "data": {
        "id": 4,
        "name": "更新的資料夾名稱",
        "arrow_path": "更新的資料夾名稱",
        "sort_order": 1
    }
}
```

#### 刪除資料夾
```http
DELETE /api/folders/{id}
Authorization: Bearer your-api-token
```

回應：
```json
{
    "message": "資料夾刪除成功"
}
```

注意：
- 刪除資料夾時，如果資料夾內有子資料夾或筆記，將無法刪除
- 刪除失敗時會返回具體的錯誤訊息：
```json
{
    "message": "資料夾內還有子資料夾，無法刪除",
    "error": "has_children"
}
```
或
```json
{
    "message": "資料夾內還有筆記，無法刪除",
    "error": "has_notes"
}
```

### 筆記 API

#### 獲取筆記列表
```http
GET /api/notes
Authorization: Bearer your-api-token
```

回應：
```json
[
    {
        "id": 1,
        "name": "測試資料夾",
        "parent_id": null,
        "is_active": 1,
        "notes": [
            {
                "id": 1,
                "title": "測試筆記",
                "content": "筆記內容",
                "is_active": 1,
                "created_at": "2024-03-20 10:00:00",
                "updated_at": "2024-03-20 10:00:00"
            }
        ]
    },
    {
        "id": null,
        "name": "未分類",
        "parent_id": null,
        "is_active": 1,
        "notes": [
            {
                "id": 2,
                "title": "未分類筆記",
                "content": "筆記內容",
                "is_active": 1,
                "created_at": "2024-03-20 10:00:00",
                "updated_at": "2024-03-20 10:00:00"
            }
        ]
    }
]
```

#### 獲取特定資料夾的筆記
```http
GET /api/notes/folders/{folder_id}
Authorization: Bearer your-api-token
```

回應：
```json
{
    "id": 1,
    "name": "測試資料夾",
    "parent_id": null,
    "is_active": 1,
    "notes": [
        {
            "id": 1,
            "title": "測試筆記",
            "content": "筆記內容",
            "is_active": 1,
            "created_at": "2024-03-20 10:00:00",
            "updated_at": "2024-03-20 10:00:00"
        }
    ]
}
```

#### 創建筆記
```http
POST /api/notes
Content-Type: application/json
Authorization: Bearer your-api-token

{
    "folder_id": 1,    // 如果 folder_id = 0，則會被視為 null（未分類筆記）
    "title": "新筆記標題",
    "content": "筆記內容"
}
```

回應：
```json
{
    "message": "筆記建立成功",
    "data": {
        "id": 1,
        "folder_id": 1,    // 如果是未分類筆記，此值為 null
        "title": "新筆記標題",
        "content": "筆記內容",
        "is_active": 1,
        "created_at": "2024-03-20 10:00:00",
        "updated_at": "2024-03-20 10:00:00"
    }
}
```

#### 更新筆記
```http
PUT /api/notes/{id}
Content-Type: application/json
Authorization: Bearer your-api-token

{
    "title": "更新的筆記標題",
    "content": "更新的筆記內容"
}
```

回應：
```json
{
    "message": "筆記更新成功",
    "data": {
        "id": 1,
        "folder_id": 1,
        "title": "更新的筆記標題",
        "content": "更新的筆記內容",
        "is_active": 1,
        "created_at": "2024-03-20 10:00:00",
        "updated_at": "2024-03-20 10:00:00"
    }
}
```

#### 刪除筆記
```http
DELETE /api/notes/{id}
Authorization: Bearer your-api-token
```

回應：
```json
{
    "message": "筆記刪除成功"
}
```

### 認證說明

所有需要認證的 API 都需要在請求標頭中加入 Bearer Token：
```http
Authorization: Bearer your-api-token
```

Token 可以通過登入 API 獲取，有效期為 1 小時。在 token 過期前，可以使用 refresh API 獲取新的 token。所有的回應都使用 JSON 格式。
