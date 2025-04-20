# note-gpt

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

### 筆記 API

#### 獲取筆記列表
```http
GET /api/notes
Authorization: Bearer your-api-token
```

可選參數：
- `folder_id`: 資料夾 ID，用於過濾特定資料夾的筆記

回應：
```json
[
    {
        "id": 1,
        "title": "筆記標題",
        "content": "筆記內容",
        "folder": {
            "id": 1,
            "name": "測試",
            "arrow_path": "測試 -> 測試01"
        },
        "created_at": "2024-01-01 00:00:00",
        "updated_at": "2024-01-01 00:00:00"
    }
]
```

### 認證說明

所有需要認證的 API 都需要在請求標頭中加入 Bearer Token：
```http
Authorization: Bearer your-api-token
```

Token 可以通過登入 API 獲取，有效期為 1 小時。在 token 過期前，可以使用 refresh API 獲取新的 token。所有的回應都使用 JSON 格式。
