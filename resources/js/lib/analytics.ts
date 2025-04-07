/**
 * Simple analytics utility for Umami
 */

// Type definitions
declare global {
    interface Window {
      umami?: {
        track: (eventName: string, data?: Record<string, any>) => void;
      }
    }
  }
  
  /**
   * Track a custom event with Umami
   */
  export const trackEvent = (eventName: string, data: Record<string, any> = {}): void => {
    if (window.umami) {
      window.umami.track(eventName, data);
      console.log(`[Analytics] Tracked: ${eventName}`, data);
    } else {
      console.log(`[Analytics] Script not loaded. Would track: ${eventName}`, data);
    }
  };
  
  /**
   * Vue composable for Umami Analytics
   */
  export const useAnalytics = () => {
    /**
     * Track a page view manually
     */
    const trackPageView = (pageName: string): void => {
      trackEvent(`view_${pageName}`);
    };
    
    return {
      trackEvent,
      trackPageView
    };
  };