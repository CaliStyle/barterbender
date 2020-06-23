/**
 * Created by thanhnc on 03/01/2017.
 */

var ynadvblog = {
    updateFavorite: function (blog_id, iType) {
        $.ajaxCall('advanced-blog.updateFavorite', $.param({iBlogId: blog_id, bFavorite: iType}));
        return false;
    }
}
