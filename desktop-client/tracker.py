import os
import sys
import time
import json
import datetime
import requests
import io
import threading
import random
from PIL import ImageGrab

CONFIG_FILE = 'config.json'

def load_local_config():
    if not os.path.exists(CONFIG_FILE):
        print(f"[{datetime.datetime.now()}] Config file {CONFIG_FILE} not found.")
        sys.exit(1)
    with open(CONFIG_FILE, 'r') as f:
        return json.load(f)

def get_server_config(server_url, api_token):
    headers = {'X-Tracking-Token': api_token}
    try:
        url = f"{server_url.rstrip('/')}/api/tracking/config"
        response = requests.get(url, headers=headers, timeout=10)
        if response.status_code == 200:
            return response.json()
        print(f"[{datetime.datetime.now()}] Failed to fetch server config: {response.status_code}")
    except Exception as e:
        print(f"[{datetime.datetime.now()}] Error fetching server config: {e}")
    return None

def send_heartbeat(server_url, api_token):
    headers = {'X-Tracking-Token': api_token}
    try:
        url = f"{server_url.rstrip('/')}/api/tracking/heartbeat"
        requests.post(url, headers=headers, timeout=5)
    except Exception:
        pass

def heartbeat_loop(server_url, api_token):
    while True:
        send_heartbeat(server_url, api_token)
        time.sleep(60) # send heartbeat every minute

def get_active_window_info():
    title = "Unknown"
    process_name = "Unknown"
    
    if sys.platform == 'win32':
        try:
            import pygetwindow as gw
            import psutil
            import win32process
            import win32gui
            
            window = win32gui.GetForegroundWindow()
            title = win32gui.GetWindowText(window)
            
            _, pid = win32process.GetWindowThreadProcessId(window)
            if pid > 0:
                process = psutil.Process(pid)
                process_name = process.name()
        except ImportError:
            pass
        except Exception as e:
            pass
    elif sys.platform == 'darwin':
        try:
            from AppKit import NSWorkspace
            active_app = NSWorkspace.sharedWorkspace().activeApplication()
            process_name = active_app['NSApplicationName']
            title = "Mac Window (Title restricted by macOS privacy)"
        except ImportError:
            pass
    return title, process_name

def upload_screenshot(server_url, api_token, quality):
    headers = {'X-Tracking-Token': api_token}
    url = f"{server_url.rstrip('/')}/api/tracking/upload"
    
    title, process = get_active_window_info()
    
    try:
        # Capture screenshot
        img = ImageGrab.grab(all_screens=True)
        # Convert to RGB to save as JPEG
        if img.mode in ("RGBA", "P"):
            img = img.convert("RGB")
            
        # Save to memory buffer
        img_byte_arr = io.BytesIO()
        img.save(img_byte_arr, format='JPEG', quality=quality)
        img_byte_arr.seek(0)
        
        files = {'image': ('screenshot.jpg', img_byte_arr, 'image/jpeg')}
        data = {
            'active_window_title': title,
            'active_process_name': process
        }
        
        # Upload
        for attempt in range(3):
            try:
                response = requests.post(url, headers=headers, files=files, data=data, timeout=30)
                if response.status_code == 200:
                    print(f"[{datetime.datetime.now()}] Screenshot uploaded successfully.")
                    return True
                else:
                    print(f"[{datetime.datetime.now()}] Upload failed with status {response.status_code}: {response.text}")
            except Exception as e:
                print(f"[{datetime.datetime.now()}] Upload exception: {e}")
            time.sleep(5)
            
    except Exception as e:
        print(f"[{datetime.datetime.now()}] Screenshot capture failed: {e}")
        
    return False

def is_office_hours(config, server_config):
    now = datetime.datetime.now()
    
    # Check working days
    if now.weekday() not in config.get('working_days', [0, 1, 2, 3, 4]):
        return False
        
    # Check hours
    try:
        current_time = now.time()
        start_time = datetime.datetime.strptime(server_config.get('office_start', '09:00:00'), '%H:%M:%S').time()
        end_time = datetime.datetime.strptime(server_config.get('office_end', '18:00:00'), '%H:%M:%S').time()
        
        if start_time <= current_time <= end_time:
            return True
    except Exception:
        # If parsing fails, default to true to not miss tracking
        return True
        
    return False

def main():
    print(f"[{datetime.datetime.now()}] Starting Tracking Client...")
    local_config = load_local_config()
    
    server_url = local_config['server_url']
    api_token = local_config['api_token']
    
    if not api_token or api_token == "replace_with_employee_api_token":
        print(f"[{datetime.datetime.now()}] Invalid API Token. Please update config.json")
        sys.exit(1)
        
    # Start heartbeat thread
    hb_thread = threading.Thread(target=heartbeat_loop, args=(server_url, api_token), daemon=True)
    hb_thread.start()
    
    while True:
        try:
            # Fetch latest config from server
            server_config = get_server_config(server_url, api_token)
            
            if not server_config:
                print(f"[{datetime.datetime.now()}] Could not reach server. Retrying in 60 seconds...")
                time.sleep(60)
                continue
                
            interval = server_config.get('interval_seconds', 600)
            quality = server_config.get('compression_quality', 60)
            
            if is_office_hours(local_config, server_config):
                print(f"[{datetime.datetime.now()}] Capturing screenshot...")
                upload_screenshot(server_url, api_token, quality)
            else:
                print(f"[{datetime.datetime.now()}] Outside office hours. Skipping capture.")
                
            # Randomize sleep time between 1 min and the configured interval
            sleep_time = random.randint(60, max(60, interval))
            print(f"[{datetime.datetime.now()}] Next capture in {sleep_time // 60} minutes and {sleep_time % 60} seconds...")
            time.sleep(sleep_time)
            
        except KeyboardInterrupt:
            print("Tracking client stopped.")
            break
        except Exception as e:
            print(f"[{datetime.datetime.now()}] Unexpected error in main loop: {e}")
            time.sleep(60)

if __name__ == '__main__':
    main()
